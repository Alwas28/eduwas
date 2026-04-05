<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\Instruktur;
use App\Models\Materi;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AiChatController extends Controller
{
    public function __construct(private AiService $ai) {}

    public function chat(Request $request, Materi $materi)
    {
        $data = $request->validate([
            'messages'           => 'required|array|min:1|max:20',
            'messages.*.role'    => 'required|in:user,assistant',
            'messages.*.content' => 'required|string|max:4000',
        ]);

        $instruktur = Instruktur::where('user_id', Auth::id())->firstOrFail();
        abort_if($materi->instruktur_id !== $instruktur->id, 403);

        $key = 'ai-chat-instruktur:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Terlalu banyak pertanyaan. Coba lagi dalam {$seconds} detik."
            ], 429);
        }
        RateLimiter::hit($key, 3600);

        $result = $this->ai->chat($data['messages'], $this->buildSystemPrompt($materi));

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 502);
        }

        return response()->json(['reply' => $result['reply']]);
    }

    private function buildSystemPrompt(Materi $materi): string
    {
        $mk     = $materi->mataKuliah?->nama ?? '';
        $prompt = "Kamu adalah asisten AI untuk instruktur pada materi \"{$materi->judul}\"";
        if ($mk) $prompt .= " di mata kuliah \"{$mk}\"";
        if ($materi->deskripsi) $prompt .= ". Deskripsi: {$materi->deskripsi}";

        if ($materi->tipe === 'teks' && $materi->konten) {
            $konten  = mb_substr(strip_tags($materi->konten ?? ''), 0, 3000);
            $prompt .= "\n\nKonten materi:\n{$konten}";
        }

        $prompt .= "\n\nTugasmu:\n"
            . "- Bantu instruktur menyiapkan, menjelaskan, atau mereview materi ini\n"
            . "- Berikan saran pengajaran, contoh soal, atau ringkasan bila diminta\n"
            . "- Jawab dalam Bahasa Indonesia yang jelas dan profesional\n"
            . "- Fokus pada konteks pengajaran dan pedagogis";

        return $prompt;
    }
}
