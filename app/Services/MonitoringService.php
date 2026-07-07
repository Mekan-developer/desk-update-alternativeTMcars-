<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MonitoringService
{
    public function getStatus(): array
    {
        return [
            'queues' => $this->queuesStatus(),
            'ws'     => $this->wsStatus(),
        ];
    }

    private function queuesStatus(): array
    {
        $checkedAt = now()->toIso8601String();

        try {
            $repository = app(\Laravel\Horizon\Contracts\MasterSupervisorRepository::class);
            $masters    = collect($repository->all());
            $ok         = $masters->contains(fn ($master) => $master->status === 'running');
            $worker     = $masters->first()?->name;
        } catch (\Throwable) {
            $ok     = false;
            $worker = null;
        }

        return [
            'ok'         => $ok,
            'pending'    => DB::table('jobs')->count(),
            'failed'     => DB::table('failed_jobs')->count(),
            'worker'     => $worker,
            'checked_at' => $checkedAt,
        ];
    }

    private function wsStatus(): array
    {
        $host = config('reverb.servers.reverb.host', '127.0.0.1');
        $port = (int) config('reverb.servers.reverb.port', 8080);

        $socket = @fsockopen($host, $port, $errno, $errstr, 1);
        $ok     = (bool) $socket;
        if ($socket) {
            fclose($socket);
        }

        return [
            'ok'         => $ok,
            'host'       => $host,
            'port'       => $port,
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
