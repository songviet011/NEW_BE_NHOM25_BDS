<?php

namespace App\Http\Controllers;

use App\Models\MoiGioi;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class SSEController extends Controller
{
    public function stream(Request $request)
    {
        $user = $this->resolveMoiGioi($request);

        if (!$user instanceof MoiGioi) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized',
            ], 401);
        }

        ignore_user_abort(true);
        set_time_limit(0);

        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        @ini_set('zlib.output_compression', '0');
        @ini_set('output_buffering', 'Off');
        @ini_set('implicit_flush', '1');

        $lastId = (int) ($request->header('Last-Event-ID') ?: $request->query('last_event_id', 0));

        return response()->stream(function () use ($user, $lastId) {
            $currentId = $lastId;

            echo "retry: 3000\n\n";
            $this->flushOutputBuffers();

            while (!connection_aborted()) {
                $newNotifications = ThongBao::where('moi_gioi_id', $user->id)
                    ->where('id', '>', $currentId)
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($newNotifications as $notification) {
                    $payload = json_encode([
                        'id' => $notification->id,
                        'khach_hang_id' => $notification->khach_hang_id,
                        'bat_dong_san_id' => $notification->bat_dong_san_id,
                        'tieu_de' => $notification->tieu_de,
                        'noi_dung' => $notification->noi_dung,
                        'trang_thai' => $notification->trang_thai,
                        'created_at' => optional($notification->created_at)->toDateTimeString(),
                    ], JSON_UNESCAPED_UNICODE);

                    echo "id: {$notification->id}\n";
                    echo "event: new-notification\n";
                    echo "data: {$payload}\n\n";

                    $currentId = $notification->id;
                    $this->flushOutputBuffers();
                }

                echo ": keepalive\n\n";
                $this->flushOutputBuffers();

                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    private function resolveMoiGioi(Request $request): ?MoiGioi
    {
        $user = Auth::guard('sanctum')->user();

        if ($user instanceof MoiGioi) {
            return $user;
        }

        $plainTextToken = $request->bearerToken()
            ?: $request->query('token')
            ?: $request->query('access_token');

        if (!$plainTextToken) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($plainTextToken);

        if (!$accessToken || !$accessToken->tokenable instanceof MoiGioi) {
            return null;
        }

        return $accessToken->tokenable;
    }

    private function flushOutputBuffers(): void
    {
        if (ob_get_level() > 0) {
            @ob_flush();
        }

        flush();
    }
}
