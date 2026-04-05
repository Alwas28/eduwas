<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnrolledToKelas extends Notification
{
    use Queueable;

    public function __construct(public Enrollment $enrollment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $kelas = $this->enrollment->kelas;
        $mk    = $kelas->mataKuliah;
        $kode  = $mk?->kode ?? '?';
        if ($kelas->kode_seksi) $kode .= '-' . $kelas->kode_seksi;

        return [
            'type'          => 'enrollment',
            'icon'          => 'fa-door-open',
            'color'         => 'emerald',
            'title'         => 'Didaftarkan ke Kelas',
            'body'          => "Anda telah didaftarkan ke kelas {$kode} — " . ($mk?->nama ?? '?'),
            'kelas_id'      => $kelas->id,
            'enrollment_id' => $this->enrollment->id,
            'url'           => '/mahasiswa/dashboard',
        ];
    }
}
