<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Models\Region;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PushController extends Controller
{
    public function index()
    {
        return Inertia::render('Push/Index', [
            'pushNotifications' => PushNotification::with('creator')->latest()->paginate(20),
            'regions'           => Region::all(),
            'tariffs'           => Tariff::where('is_active', true)->get(),
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'target'    => 'required|in:all,selected,filtered',
            'user_ids'  => 'nullable|array',
            'filters'   => 'nullable|array',
            'link_type' => 'nullable|string',
            'link_id'   => 'nullable|integer',
        ]);

        $users = $this->resolveTargetUsers($data);
        $tokens = $users->whereNotNull('fcm_token')->pluck('fcm_token');

        if ($tokens->isNotEmpty() && config('services.fcm.server_key')) {
            foreach ($tokens->chunk(500) as $batch) {
                Http::withToken(config('services.fcm.server_key'))
                    ->post('https://fcm.googleapis.com/fcm/send', [
                        'registration_ids' => $batch->values(),
                        'notification'     => ['title' => $data['title'], 'body' => $data['body']],
                        'data'             => ['link_type' => $data['link_type'] ?? null, 'link_id' => $data['link_id'] ?? null],
                    ]);
            }
        }

        PushNotification::create([
            'title'      => $data['title'],
            'body'       => $data['body'],
            'target'     => $data['target'],
            'user_ids'   => $data['user_ids'] ?? null,
            'filters'    => $data['filters'] ?? null,
            'link_type'  => $data['link_type'] ?? null,
            'link_id'    => $data['link_id'] ?? null,
            'sent_count' => $users->count(),
            'sent_at'    => now(),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Уведомление отправлено (' . $users->count() . ' получателей)']);
    }

    private function resolveTargetUsers(array $data)
    {
        $query = User::where('role', 'user')->where('status', 'active');

        if ($data['target'] === 'selected' && ! empty($data['user_ids'])) {
            $query->whereIn('id', $data['user_ids']);
        } elseif ($data['target'] === 'filtered' && ! empty($data['filters'])) {
            $filters = $data['filters'];
            if (! empty($filters['region_id'])) {
                $query->where('region_id', $filters['region_id']);
            }
            if (! empty($filters['tariff_id'])) {
                $query->where('tariff_id', $filters['tariff_id']);
            }
        }

        return $query->get();
    }
}
