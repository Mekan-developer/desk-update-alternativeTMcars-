<?php

namespace App\Jobs;

use App\Models\Video;
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

    public function handle(): void
    {
        $video       = Video::findOrFail($this->videoId);
        $inputPath   = Storage::disk('public')->path($video->original_path);
        $outputName  = pathinfo($video->original_path, PATHINFO_FILENAME) . '_processed.mp4';
        $outputPath  = Storage::disk('public')->path('videos/' . $outputName);

        $cmd = sprintf(
            'ffmpeg -i %s -c:v libx264 -preset fast -crf 23 -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
            escapeshellarg($inputPath),
            escapeshellarg($outputPath),
        );

        exec($cmd, $output, $code);

        if ($code !== 0) {
            Log::error('FFmpeg failed', ['code' => $code, 'output' => implode("\n", $output)]);
            $this->fail('FFmpeg processing failed');
            return;
        }

        $video->update([
            'processed_path' => 'videos/' . $outputName,
            'is_processed'   => true,
        ]);
    }
}
