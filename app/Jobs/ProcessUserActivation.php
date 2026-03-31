<?php

namespace App\Jobs;

use App\Mail\AuthMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ProcessUserActivation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = Str::random(60);

        DB::table('tr_password_reset_tokens')->updateOrInsert(
            ['email' => $this->user->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($token), 'created_at' => now()]
        );

         $link = env('FRONTEND_URL', 'http://localhost:5173') . '/set-password?token=' . $token . '&email=' . urlencode($this->user->email);

        try {
            Mail::to($this->user->email)->send(new AuthMail($this->user, $link, 'activation'));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email aktivasi ke ' . $this->user->email . 'Error: ' . $e->getMessage());
        }
    }
}
