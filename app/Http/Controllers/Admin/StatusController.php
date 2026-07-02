<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'horizon' => $this->checkHorizon(),
            'reverb'  => $this->checkReverb(),
        ]);
    }

    private function checkHorizon(): bool
    {
        try {
            $repository = app(\Laravel\Horizon\Contracts\MasterSupervisorRepository::class);
            $masters = $repository->all();
            return collect($masters)->contains(
                fn($master) => $master->status === 'running'
            );
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkReverb(): bool
    {
        $host = config('reverb.servers.reverb.host', '127.0.0.1');
        $port = (int) config('reverb.servers.reverb.port', 8080);

        $socket = @fsockopen($host, $port, $errno, $errstr, 1);
        if ($socket) {
            fclose($socket);
            return true;
        }

        return false;
    }
}
