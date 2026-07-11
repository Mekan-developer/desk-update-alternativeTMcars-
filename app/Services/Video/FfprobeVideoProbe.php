<?php

namespace App\Services\Video;

class FfprobeVideoProbe implements VideoProbeInterface
{
    public function duration(string $absolutePath): ?float
    {
        $cmd = sprintf(
            'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>&1',
            escapeshellarg($absolutePath),
        );

        exec($cmd, $output, $code);

        $duration = trim(implode("\n", $output));

        if ($code !== 0 || ! is_numeric($duration)) {
            return null;
        }

        return (float) $duration;
    }
}
