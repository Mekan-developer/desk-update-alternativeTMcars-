<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\Video\VideoProbeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 300;

    public function __construct(
        private readonly int $videoId,
    ) {
        $this->onQueue('media');
    }

    /**
     * Сжимает оригинал (H.264, ширина ≤720 — вертикальные ролики с телефона)
     * и вырезает превью-кадр. Файлы кладутся рядом с оригиналом
     * в папке ролика videos/{uuid}/ (см. VideoService::createFromApi).
     */
    public function handle(VideoProbeInterface $probe): void
    {
        $video = Video::findOrFail($this->videoId);
        $disk  = Storage::disk('public');

        $inputPath    = $disk->path($video->path);
        $dir          = dirname($video->path);
        $processedRel = $dir.'/processed.mp4';
        $previewRel   = $dir.'/preview.jpg';

        $cmd = sprintf(
            'ffmpeg -y -i %s -vf %s -c:v libx264 -preset fast -crf 23 -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
            escapeshellarg($inputPath),
            escapeshellarg('scale=trunc(min(iw\,720)/2)*2:-2'),
            escapeshellarg($disk->path($processedRel)),
        );

        exec($cmd, $output, $code);

        if ($code !== 0) {
            Log::error('FFmpeg failed', ['video_id' => $video->id, 'code' => $code, 'output' => implode("\n", $output)]);
            $this->fail('FFmpeg processing failed');
            return;
        }

        // Превью-кадр не критичен: в админке есть заглушка с иконкой play
        $previewCmd = sprintf(
            'ffmpeg -y -ss 0.5 -i %s -frames:v 1 -vf %s %s 2>&1',
            escapeshellarg($inputPath),
            escapeshellarg('scale=trunc(min(iw\,360)/2)*2:-2'),
            escapeshellarg($disk->path($previewRel)),
        );

        exec($previewCmd, $previewOutput, $previewCode);

        if ($previewCode !== 0) {
            Log::warning('FFmpeg preview frame failed', ['video_id' => $video->id, 'output' => implode("\n", $previewOutput)]);
        }

        $video->update([
            'processed_path'   => $processedRel,
            'preview_path'     => $previewCode === 0 ? $previewRel : null,
            'is_processed'     => true,
            // Подстраховка: если при загрузке длительность не определилась
            'duration_seconds' => $video->duration_seconds ?: (int) round($probe->duration($inputPath) ?? 0),
        ]);
    }
}
