<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Models\Enrollment;
use App\Models\Mahasiswa;
use App\Models\PokokBahasan;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AiChatController extends Controller
{
    public function __construct(private AiService $ai) {}

    /**
     * Load chat history for a PB.
     */
    public function history(Request $request, PokokBahasan $pokokBahasan)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $messages = AiChatMessage::where('mahasiswa_id', $mahasiswa->id)
            ->where('pb_id', $pokokBahasan->id)
            ->orderBy('created_at')
            ->get(['role', 'content']);

        return response()->json(['messages' => $messages]);
    }

    /**
     * Send a message and get AI reply. History is loaded from DB.
     */
    public function chat(Request $request, PokokBahasan $pokokBahasan)
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'message'  => 'required|string|max:4000',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();
        Enrollment::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $data['kelas_id'])
            ->firstOrFail();

        $key = 'ai-chat:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 30)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Terlalu banyak pertanyaan. Coba lagi dalam {$seconds} detik."
            ], 429);
        }
        RateLimiter::hit($key, 3600);

        // Save user message first
        AiChatMessage::create([
            'mahasiswa_id' => $mahasiswa->id,
            'pb_id'        => $pokokBahasan->id,
            'role'         => 'user',
            'content'      => $data['message'],
        ]);

        // Build messages array from DB (last 20 for context window)
        $history = AiChatMessage::where('mahasiswa_id', $mahasiswa->id)
            ->where('pb_id', $pokokBahasan->id)
            ->orderBy('created_at')
            ->get(['role', 'content'])
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $result = $this->ai->chat($history, $this->buildSystemPrompt($pokokBahasan));

        if (isset($result['error'])) {
            // Roll back the user message so DB stays consistent
            AiChatMessage::where('mahasiswa_id', $mahasiswa->id)
                ->where('pb_id', $pokokBahasan->id)
                ->latest()
                ->first()
                ?->delete();

            return response()->json(['error' => $result['error']], 502);
        }

        // Save assistant reply
        AiChatMessage::create([
            'mahasiswa_id' => $mahasiswa->id,
            'pb_id'        => $pokokBahasan->id,
            'role'         => 'assistant',
            'content'      => $result['reply'],
        ]);

        return response()->json(['reply' => $result['reply']]);
    }

    private function buildSystemPrompt(PokokBahasan $pokokBahasan): string
    {
        $aiName = $this->ai->assistantName();
        $mk     = $pokokBahasan->mataKuliah?->nama ?? '';

        $prompt = "Kamu adalah {$aiName}, asisten belajar AI untuk topik \"{$pokokBahasan->judul}\"";
        if ($mk) $prompt .= " pada mata kuliah \"{$mk}\"";
        if ($pokokBahasan->deskripsi) $prompt .= ". Deskripsi topik: {$pokokBahasan->deskripsi}";

        $prompt .= "\n\nTugasmu:\n"
            . "- Bantu mahasiswa memahami dan mengeksplorasi topik ini\n"
            . "- Jawab dalam Bahasa Indonesia yang jelas dan mudah dipahami\n"
            . "- Berikan contoh nyata bila perlu\n"
            . "- Jika pertanyaan tidak relevan dengan topik, arahkan kembali\n"
            . "- Jangan memberikan jawaban tugas/ujian secara langsung, tapi bantu proses berpikir";

        return $prompt;
    }
}
