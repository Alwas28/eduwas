<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    public function assistantName(): string
    {
        return Setting::get('ai_assistant_name', 'Tanya Asdos');
    }

    /**
     * Send a chat completion request to OpenRouter.
     *
     * @param  array  $messages   Array of ['role'=>'user'|'assistant', 'content'=>string]
     * @param  string $systemPrompt
     * @return array  ['reply' => string] or ['error' => string]
     */
    public function chat(array $messages, string $systemPrompt): array
    {
        $apiKey = Setting::get('openrouter_api_key');
        $model  = Setting::get('openrouter_model', 'google/gemma-3-27b-it:free');

        if (!$apiKey) {
            return ['error' => 'Fitur AI Chat belum dikonfigurasi. Hubungi administrator.'];
        }

        // Beberapa model (termasuk Gemma) tidak support role 'system'.
        // OpenRouter biasanya mengkonversi, tapi sebagai fallback kita gabungkan
        // system prompt ke pesan user pertama jika model adalah Gemma.
        $isGemma    = str_contains($model, 'gemma');
        $allMessages = $isGemma
            ? $this->injectSystemAsUser($messages, $systemPrompt)
            : array_merge([['role' => 'system', 'content' => $systemPrompt]], $messages);

        $payload = [
            'model'      => $model,
            'max_tokens' => 1024,
            'messages'   => $allMessages,
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                    'HTTP-Referer'  => config('app.url', 'http://localhost'),
                    'X-Title'       => config('app.name', 'EduLearn LMS'),
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', $payload);

            if (!$response->successful()) {
                $body = $response->json();
                $msg  = $body['error']['message']
                     ?? $body['error']['code']
                     ?? ('HTTP ' . $response->status());

                Log::warning('OpenRouter error', [
                    'status'  => $response->status(),
                    'model'   => $model,
                    'body'    => $body,
                ]);

                return ['error' => "AI gagal merespons ({$msg}). Coba lagi atau hubungi administrator."];
            }

            $reply = $response->json('choices.0.message.content', '');

            if ($reply === '' || $reply === null) {
                Log::warning('OpenRouter returned empty reply', ['body' => $response->json()]);
                return ['error' => 'AI tidak menghasilkan respons. Coba lagi.'];
            }

            return ['reply' => $reply];

        } catch (\Throwable $e) {
            Log::error('AiService exception', ['message' => $e->getMessage()]);
            return ['error' => 'Koneksi ke AI gagal. Coba beberapa saat lagi.'];
        }
    }

    /**
     * Merge system prompt ke pesan user pertama (untuk model tanpa system role).
     */
    private function injectSystemAsUser(array $messages, string $systemPrompt): array
    {
        if (empty($messages)) {
            return [['role' => 'user', 'content' => $systemPrompt]];
        }

        $first = $messages[0];
        if ($first['role'] === 'user') {
            $messages[0]['content'] = $systemPrompt . "\n\n---\n\n" . $first['content'];
        } else {
            array_unshift($messages, ['role' => 'user', 'content' => $systemPrompt]);
        }

        return $messages;
    }
}
