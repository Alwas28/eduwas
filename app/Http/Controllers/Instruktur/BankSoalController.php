<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\BankSoalPilihan;
use App\Models\Instruktur;
use App\Models\MataKuliah;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser as PdfParser;

class BankSoalController extends Controller
{
    private function getInstruktur(): Instruktur
    {
        return Instruktur::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Daftar bank soal per mata kuliah.
     */
    public function index(Request $request)
    {
        $instruktur = $this->getInstruktur();

        // Mata kuliah dari semua kelas yang diampu (unique)
        $mataKuliahList = MataKuliah::whereIn('id',
            $instruktur->kelas()->pluck('mata_kuliah_id')
        )->orderBy('nama')->get();

        $mkId    = $request->query('mk');
        $mk      = $mkId ? $mataKuliahList->firstWhere('id', $mkId) : $mataKuliahList->first();

        $soalList = collect();
        if ($mk) {
            $soalList = BankSoal::with('pilihan')
                ->where('instruktur_id', $instruktur->id)
                ->where('mata_kuliah_id', $mk->id)
                ->orderBy('tipe')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('instruktur.bank-soal.index', compact(
            'instruktur', 'mataKuliahList', 'mk', 'soalList'
        ));
    }

    /**
     * Simpan soal baru (manual).
     */
    public function store(Request $request)
    {
        $instruktur = $this->getInstruktur();

        $data = $request->validate([
            'mata_kuliah_id'   => ['required', 'exists:mata_kuliah,id'],
            'tipe'             => ['required', 'in:essay,pilihan_ganda'],
            'pertanyaan'       => ['required', 'string'],
            'tingkat_kesulitan'=> ['required', 'in:mudah,sedang,sulit'],
            'bobot'            => ['required', 'integer', 'min:1', 'max:100'],
            'pembahasan'       => ['nullable', 'string'],
            // Pilihan ganda
            'pilihan'          => ['required_if:tipe,pilihan_ganda', 'array', 'min:2'],
            'pilihan.*.teks'   => ['required_if:tipe,pilihan_ganda', 'string'],
            'pilihan_benar'    => ['required_if:tipe,pilihan_ganda', 'integer'],
        ]);

        DB::transaction(function () use ($instruktur, $data, $request) {
            $soal = BankSoal::create([
                'instruktur_id'    => $instruktur->id,
                'mata_kuliah_id'   => $data['mata_kuliah_id'],
                'tipe'             => $data['tipe'],
                'pertanyaan'       => $data['pertanyaan'],
                'tingkat_kesulitan'=> $data['tingkat_kesulitan'],
                'bobot'            => $data['bobot'],
                'pembahasan'       => $data['pembahasan'] ?? null,
            ]);

            if ($data['tipe'] === 'pilihan_ganda') {
                $hurufList = ['A','B','C','D','E'];
                foreach ($request->pilihan as $i => $p) {
                    BankSoalPilihan::create([
                        'bank_soal_id' => $soal->id,
                        'huruf'        => $hurufList[$i] ?? chr(65 + $i),
                        'teks'         => $p['teks'],
                        'is_benar'     => ($i == $request->pilihan_benar),
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Soal berhasil ditambahkan.']);
    }

    /**
     * Update soal.
     */
    public function update(Request $request, BankSoal $bankSoal)
    {
        $instruktur = $this->getInstruktur();
        abort_if($bankSoal->instruktur_id !== $instruktur->id, 403);

        $data = $request->validate([
            'pertanyaan'        => ['required', 'string'],
            'tingkat_kesulitan' => ['required', 'in:mudah,sedang,sulit'],
            'bobot'             => ['required', 'integer', 'min:1', 'max:100'],
            'pembahasan'        => ['nullable', 'string'],
            'pilihan'           => ['required_if:tipe,pilihan_ganda', 'array', 'min:2'],
            'pilihan.*.teks'    => ['required_if:tipe,pilihan_ganda', 'string'],
            'pilihan_benar'     => ['required_if:tipe,pilihan_ganda', 'integer'],
        ]);

        DB::transaction(function () use ($bankSoal, $data, $request) {
            $bankSoal->update([
                'pertanyaan'        => $data['pertanyaan'],
                'tingkat_kesulitan' => $data['tingkat_kesulitan'],
                'bobot'             => $data['bobot'],
                'pembahasan'        => $data['pembahasan'] ?? null,
            ]);

            if ($bankSoal->tipe === 'pilihan_ganda') {
                $bankSoal->pilihan()->delete();
                $hurufList = ['A','B','C','D','E'];
                foreach ($request->pilihan as $i => $p) {
                    BankSoalPilihan::create([
                        'bank_soal_id' => $bankSoal->id,
                        'huruf'        => $hurufList[$i] ?? chr(65 + $i),
                        'teks'         => $p['teks'],
                        'is_benar'     => ($i == $request->pilihan_benar),
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Soal berhasil diperbarui.']);
    }

    /**
     * Hapus soal.
     */
    public function destroy(BankSoal $bankSoal)
    {
        $instruktur = $this->getInstruktur();
        abort_if($bankSoal->instruktur_id !== $instruktur->id, 403);
        $bankSoal->delete();
        return response()->json(['message' => 'Soal berhasil dihapus.']);
    }

    /**
     * Generate soal via AI dari PDF materi.
     */
    public function aiGenerate(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'pdf'            => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'jumlah_essay'   => ['integer', 'min:0', 'max:20'],
            'jumlah_pg'      => ['integer', 'min:0', 'max:20'],
        ]);

        // Ekstrak teks PDF
        try {
            $parser  = new PdfParser();
            $pdf     = $parser->parseFile($request->file('pdf')->getRealPath());
            $rawText = $pdf->getText();
            $text    = preg_replace('/\s+/', ' ', trim($rawText));
            $text    = mb_substr($text, 0, 5000);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal membaca PDF. Pastikan file tidak terenkripsi.'], 422);
        }

        if (strlen($text) < 100) {
            return response()->json(['error' => 'Teks PDF terlalu pendek atau tidak dapat dibaca.'], 422);
        }

        $jumlahEssay = max(0, (int) $request->get('jumlah_essay', 3));
        $jumlahPg    = max(0, (int) $request->get('jumlah_pg', 5));

        if ($jumlahEssay === 0 && $jumlahPg === 0) {
            return response()->json(['error' => 'Tentukan jumlah soal yang ingin dibuat.'], 422);
        }

        $mk = MataKuliah::find($request->mata_kuliah_id);

        $prompt = <<<PROMPT
Kamu adalah asisten pembuat soal ujian untuk mata kuliah "{$mk->nama}".

Berdasarkan materi berikut, buatkan soal ujian dalam format JSON yang valid.
Jangan tambahkan teks apapun di luar JSON.

Materi:
{$text}

Buat:
- {$jumlahEssay} soal essay
- {$jumlahPg} soal pilihan ganda (4 pilihan A/B/C/D, tepat 1 jawaban benar)

Format JSON yang harus dikembalikan:
{
  "essay": [
    {
      "pertanyaan": "...",
      "pembahasan": "...",
      "tingkat_kesulitan": "mudah|sedang|sulit"
    }
  ],
  "pilihan_ganda": [
    {
      "pertanyaan": "...",
      "pilihan": ["teks A", "teks B", "teks C", "teks D"],
      "jawaban_benar": 0,
      "pembahasan": "...",
      "tingkat_kesulitan": "mudah|sedang|sulit"
    }
  ]
}

Catatan: jawaban_benar adalah index (0=A, 1=B, 2=C, 3=D).
PROMPT;

        $ai     = app(AiService::class);
        $result = $ai->chat([['role' => 'user', 'content' => $prompt]], '');

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 422);
        }

        // Parse JSON dari respons AI
        $reply = $result['reply'];
        // Ambil blok JSON jika ada markdown code block
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $reply, $m)) {
            $reply = trim($m[1]);
        }
        // Cari JSON object
        if (preg_match('/(\{[\s\S]*\})/u', $reply, $m)) {
            $reply = $m[1];
        }

        $data = json_decode($reply, true);
        if (!$data) {
            return response()->json(['error' => 'AI tidak mengembalikan format yang valid. Coba lagi.'], 422);
        }

        return response()->json([
            'essay'        => $data['essay']        ?? [],
            'pilihan_ganda'=> $data['pilihan_ganda'] ?? [],
        ]);
    }

    /**
     * Simpan batch soal hasil AI yang dipilih.
     */
    public function aiSave(Request $request)
    {
        $instruktur = $this->getInstruktur();

        $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'soal'           => ['required', 'array', 'min:1'],
        ]);

        DB::transaction(function () use ($instruktur, $request) {
            foreach ($request->soal as $item) {
                $tipe = $item['tipe'];
                $soal = BankSoal::create([
                    'instruktur_id'    => $instruktur->id,
                    'mata_kuliah_id'   => $request->mata_kuliah_id,
                    'tipe'             => $tipe,
                    'pertanyaan'       => $item['pertanyaan'],
                    'tingkat_kesulitan'=> $item['tingkat_kesulitan'] ?? 'sedang',
                    'bobot'            => $tipe === 'essay' ? 20 : 5,
                    'pembahasan'       => $item['pembahasan'] ?? null,
                ]);

                if ($tipe === 'pilihan_ganda' && !empty($item['pilihan'])) {
                    $hurufList = ['A','B','C','D','E'];
                    foreach ($item['pilihan'] as $i => $teks) {
                        BankSoalPilihan::create([
                            'bank_soal_id' => $soal->id,
                            'huruf'        => $hurufList[$i] ?? chr(65 + $i),
                            'teks'         => $teks,
                            'is_benar'     => ($i == ($item['jawaban_benar'] ?? -1)),
                        ]);
                    }
                }
            }
        });

        return response()->json(['message' => 'Soal berhasil disimpan ke bank soal.']);
    }
}
