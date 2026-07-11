<?php

namespace App\Services;

use App\Actions\CheckVideoLimitAction;
use App\Jobs\ProcessVideoJob;
use App\Models\User;
use App\Models\Video;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    public function __construct(
        private readonly VideoRepositoryInterface $videoRepository,
        private readonly CheckVideoLimitAction $checkVideoLimitAction,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->videoRepository->paginate($filters);
    }

    public function counts(): array
    {
        return [
            'pending'  => $this->videoRepository->countByStatus('pending'),
            'approved' => $this->videoRepository->countByStatus('approved'),
            'rejected' => $this->videoRepository->countByStatus('rejected'),
        ];
    }

    public function approve(Video $video): void
    {
        $this->videoRepository->update($video, [
            'status'              => 'approved',
            'rejection_reason_id' => null,
        ]);
    }

    public function reject(Video $video, int $rejectionReasonId): void
    {
        $this->videoRepository->update($video, [
            'status'              => 'rejected',
            'rejection_reason_id' => $rejectionReasonId,
        ]);
    }

    /** Удаляет ролик вместе с файлами (оригинал + сжатая версия + превью-кадр) */
    public function delete(Video $video): void
    {
        // Файлы ролика лежат в собственной папке videos/{uuid} (см. createFromApi)
        $dir = dirname($video->path);
        if (! in_array($dir, ['.', '', 'videos'], true)) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        // Старые записи могли хранить файлы плоско — подчищаем и их
        Storage::disk('public')->delete(array_filter([
            $video->path, $video->processed_path, $video->preview_path,
        ]));

        $this->videoRepository->delete($video);
    }

    // ─── Mobile API (ТЗ §7) ─────────────────────────────────────────────────

    /** Публичная вертикальная лента: только одобренные ролики */
    public function feedForApi(array $filters, int $perPage = 20, ?User $viewer = null): LengthAwarePaginator
    {
        return $this->videoRepository->paginateForApi($filters, $perPage, $viewer?->id);
    }

    public function myVideos(User $user, array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->videoRepository->paginateByUser($user->id, $filters, $perPage);
    }

    /**
     * Загрузка ролика из приложения: квота тарифа уже проверена экшеном,
     * длительность (≤60 сек) — валидацией. Файл уходит в очередь на сжатие.
     */
    public function createFromApi(User $user, array $data, int $durationSeconds): Video
    {
        $this->checkVideoLimitAction->execute($user);

        /** @var UploadedFile $file */
        $file = $data['video'];

        $extension = strtolower($file->getClientOriginalExtension() ?: 'mp4');
        $path      = $file->storeAs('videos/'.Str::uuid(), 'original.'.$extension, 'public');

        $video = $this->videoRepository->create([
            'user_id'          => $user->id,
            'title'            => $data['title'],
            'tags'             => $data['tags'] ?? [],
            'path'             => $path,
            'duration_seconds' => $durationSeconds,
            'status'           => 'pending',
        ]);

        ProcessVideoJob::dispatch($video->id);

        return $this->videoRepository->find($video->id);
    }

    /** @return array{is_liked: bool, likes_count: int} */
    public function toggleLike(Video $video, User $user): array
    {
        return $this->videoRepository->toggleLike($video, $user->id);
    }

    public function loadLikeFlag(Video $video, ?User $viewer): void
    {
        $this->videoRepository->loadLikeFlag($video, $viewer?->id);
    }

    /** Счётчик просмотров: атомарный инкремент, свои просмотры не считаются */
    public function registerView(Video $video, ?User $viewer): void
    {
        if ($viewer && $viewer->id === $video->user_id) {
            return;
        }

        $this->videoRepository->incrementViews($video);
    }
}
