<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    private const ALLOWED_KEYS = ['ai_assistant_name', 'openrouter_api_key', 'openrouter_model'];

    private const MODEL_OPTIONS = [
        // ── Gratis (butuh saldo $1+ di openrouter.ai) ────────────
        'google/gemma-3-27b-it:free'                  => '[Gratis] Google Gemma 3 27B',
        'google/gemma-3-4b-it:free'                   => '[Gratis] Google Gemma 3 4B',
        'meta-llama/llama-3.3-70b-instruct:free'      => '[Gratis] Meta Llama 3.3 70B',
        'meta-llama/llama-3.1-8b-instruct:free'       => '[Gratis] Meta Llama 3.1 8B',
        'qwen/qwen-2.5-7b-instruct:free'              => '[Gratis] Qwen 2.5 7B',
        'deepseek/deepseek-r1-distill-qwen-32b:free'  => '[Gratis] DeepSeek R1 32B',
        'mistralai/mistral-small-3.1-24b-instruct:free' => '[Gratis] Mistral Small 3.1 24B',
        // ── Berbayar (per token, tidak butuh saldo minimum) ──────
        'openai/gpt-4o-mini'                          => '[Berbayar] OpenAI GPT-4o Mini',
        'google/gemini-flash-1.5'                     => '[Berbayar] Google Gemini 1.5 Flash',
        'anthropic/claude-haiku-4-5'                  => '[Berbayar] Anthropic Claude Haiku',
    ];

    public function index()
    {
        $settings = Setting::whereIn('key', self::ALLOWED_KEYS)->get()->keyBy('key');
        $modelOptions = self::MODEL_OPTIONS;

        return view('admin.pengaturan.index', compact('settings', 'modelOptions'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ai_assistant_name'  => 'sometimes|string|max:60|min:2',
            'openrouter_api_key' => 'sometimes|nullable|string|max:200',
            'openrouter_model'   => 'sometimes|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS)) {
                // Don't overwrite API key with empty string if user left it blank
                if ($key === 'openrouter_api_key' && ($value === null || $value === '')) {
                    continue;
                }
                Setting::set($key, $value);
            }
        }

        return response()->json(['message' => 'Pengaturan berhasil disimpan.']);
    }
}
