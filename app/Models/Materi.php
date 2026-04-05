<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'mata_kuliah_id', 'instruktur_id', 'pokok_bahasan_id', 'judul', 'deskripsi',
        'tipe', 'file_path', 'nama_file', 'ukuran_file',
        'url', 'konten', 'urutan', 'status', 'allow_download',
    ];

    protected $casts = [
        'ukuran_file'    => 'integer',
        'urutan'         => 'integer',
        'allow_download' => 'boolean',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class);
    }

    public function pokokBahasan()
    {
        return $this->belongsTo(PokokBahasan::class);
    }

    public function akses()
    {
        return $this->hasMany(MateriAkses::class);
    }

    /** URL publik file (jika tipe dokumen) */
    public function fileUrl(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /** Ukuran file dalam format human-readable */
    public function ukuranHuman(): string
    {
        if (!$this->ukuran_file) return '—';
        $bytes = $this->ukuran_file;
        if ($bytes >= 1_048_576) return number_format($bytes / 1_048_576, 1) . ' MB';
        if ($bytes >= 1_024)    return number_format($bytes / 1_024, 1) . ' KB';
        return $bytes . ' B';
    }

    /** Icon FontAwesome berdasarkan tipe */
    public function tipeIcon(): string
    {
        return match($this->tipe) {
            'dokumen' => 'fa-file-lines',
            'video'   => 'fa-circle-play',
            'link'    => 'fa-link',
            'teks'    => 'fa-align-left',
            default   => 'fa-file',
        };
    }

    /** Warna tema berdasarkan tipe */
    public function tipeColor(): string
    {
        return match($this->tipe) {
            'dokumen' => 'bg-blue-500/15 text-blue-400',
            'video'   => 'bg-rose-500/15 text-rose-400',
            'link'    => 'bg-violet-500/15 text-violet-400',
            'teks'    => 'bg-amber-500/15 text-amber-400',
            default   => 'a-bg-lt a-text',
        };
    }

    /** Label tipe */
    public function tipeLabel(): string
    {
        return match($this->tipe) {
            'dokumen' => 'Dokumen',
            'video'   => 'Video',
            'link'    => 'Tautan',
            'teks'    => 'Teks',
            default   => ucfirst($this->tipe),
        };
    }
}
