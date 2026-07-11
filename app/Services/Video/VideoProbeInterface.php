<?php

namespace App\Services\Video;

interface VideoProbeInterface
{
    /**
     * Длительность видеофайла в секундах.
     * null — если файл не удалось прочитать как видео.
     */
    public function duration(string $absolutePath): ?float;
}
