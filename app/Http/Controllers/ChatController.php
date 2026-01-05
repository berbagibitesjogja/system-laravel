<?php

namespace App\Http\Controllers;

use App\Models\Donation\Donation;
use App\Models\Donation\Food;
use App\Models\Donation\Sponsor;
use App\Models\Heroes\Hero;
use App\Models\Heroes\University;
use Gemini\Data\GenerationConfig;
use Gemini;

class ChatController extends Controller
{
    public function chat($text)
    {
        if (!$this->isContextualChat($text)) {
            return ["Maaf, saya hanya bisa menjawab pertanyaan seputar Berbagi Bites Jogja (BBJ). 😊", 0];
        }

        $chat = $this->analyzeChat($text);
        return $chat;
    }

    private function isContextualChat($text)
    {
        $prompt = "Tugas kamu adalah memeriksa apakah teks berikut masih dalam konteks pembicaraan tentang organisasi Berbagi Bites Jogja (BBJ), yaitu komunitas non-profit yang bergerak di bidang food rescue dan food bank di Yogyakarta. Jika teks tersebut membahas BBJ, kontak, makanan, donasi, relawan, food waste, distribusi makanan, atau hal yang berkaitan dengan kegiatan sosial BBJ, maka jawab *true*. Jika teks tidak relevan, seperti menanyakan hal di luar BBJ, politik, hiburan, keuangan pribadi, atau topik umum lainnya yang tidak berhubungan, maka jawab *false*. Jangan beri penjelasan apapun, cukup satu kata saja yaitu *true* atau *false*. Berikut teks yang perlu dicek: \"$text\"";

        $result = $this->sendGemini($prompt);
        return trim(strtolower($result[0])) === 'true';
    }

    public function latestDonation()
    {
        $donations = Donation::with('sponsor:id,name')
            ->latest()
            ->limit(10)
            ->get(['quota', 'status', 'take', 'sponsor_id']);

        $donations = $donations->map(function ($donation) {
            return [
                'quota' => $donation->quota,
                'status' => $donation->status,
                'take' => $donation->take,
                'sponsor_name' => $donation->sponsor ? $donation->sponsor->name : null,
            ];
        });
        return $donations;
    }

    private function foodData()
    {
        return Food::get(['name', 'weight']);
    }

    private function heroesData()
    {
        return University::withSum('heroes', 'quantity')->get(['id', 'name']);
    }

    private function sponsorData()
    {
        return Sponsor::withCount('donation')
            ->where('variant', 'company')
            ->get()
            ->map(function ($sponsor) {
                return [
                    'name' => $sponsor->name,
                    'donation_count' => $sponsor->donation_count,
                ];
            });
    }

    private function material()
    {
        $weight = Food::all()->sum('weight') / 1000;
        $count = Donation::all()->count();
        $hero = Hero::all()->sum('quantity');
        $proposal = "Berbagi Bites Jogja (BBJ) adalah program non-profit yang bertujuan mengurangi limbah makanan dan kelaparan di Yogyakarta melalui inisiatif food rescue dan food bank. BBJ menghubungkan donatur makanan dengan penerima manfaat (food heroes) seperti mahasiswa UGM dan masyarakat rentan, menciptakan dampak lingkungan dan sosial. Sejak peluncuran pada 22 Juni 2024, BBJ telah menyelamatkan $weight kg makanan dalam $count aksi, menjangkau $hero food heroes. Visi BBJ adalah kelestarian lingkungan dan kesejahteraan kemanusiaan melalui kerja sama berkelanjutan. Program utamanya meliputi pendistribusian makanan layak konsumsi (Food Rescue) dan penyimpanan pangan untuk kegiatan komunitas (Food Bank), dengan protokol keamanan dari sumber hingga distribusi.";

        $proposalFund = "Limbah makanan di Yogyakarta mencapai 54,6% dari total sampah, menunjukkan urgensi pengelolaan limbah yang berkelanjutan. BBJ mengajak mitra strategis untuk berkolaborasi dalam mengurangi food waste, mendukung edukasi pangan, dan menanggulangi kelaparan. Sejak berdiri, BBJ telah mendistribusikan lebih dari $weight kg makanan ke lebih dari $hero orang. BBJ bermitra dengan Sheraton, Holland Bakery, Artotel, dan produsen makanan beku. Bentuk kerja sama akan disepakati melalui MoU.";

        $contact = "Untuk informasi lebih lanjut, hubungi kami melalui WhatsApp di *08986950700*, Instagram _@berbagibitesjogja_ , atau email ke *berbagibitesjogja@gmail.com*, dan website berbagibitesjogja.com.";

        return "Berikut gambaran umum: $proposal\n\nBerikut ringkasan proposal keuangan: $proposalFund\n\nKontak resmi BBJ: $contact";
    }


    private function data()
    {
        $sponsor = json_encode($this->sponsorData());
        $heroes = json_encode($this->heroesData());
        $food = json_encode($this->foodData());
        $latest = json_encode($this->latestDonation());

        return "Data donatur dalam JSON: ```$sponsor```\nData penerima dalam JSON: ```$heroes```\nData makanan dalam JSON: ```$food```\nData donasi terbaru dalam JSON: ```$latest```";
    }

    private function analyzeChat($text)
    {
        $material = $this->material();
        $data = $this->data();

        $intro = "Kamu adalah chatbot komunitas BBJ, organisasi food rescue dan food bank di Yogyakarta. Jawabanmu harus relevan dengan konteks BBJ dan disampaikan dalam format *plain text WhatsApp*, boleh menggunakan *bold*, _italic_, dan emoticon secukupnya. Jika ditanya di luar BBJ, jawab bahwa kamu hanya fokus pada BBJ. Jika ada selisih antara materi dan data terbaru, utamakan data terbaru. Hindari penjelasan berlebihan dan pertimbangkan privasi data BBJ. Apabila dalam bentuk jumlah maka berikan rentang saja.";
        $prompt = "$intro\n\nPrompt dari pengguna: $text\n\nMateri: $material\n\nData terbaru:\n$data";

        return $this->sendGemini($prompt);
    }

    private function sendGemini($text)
    {
        $generationConfig = new GenerationConfig(
            maxOutputTokens: 3000,
        );
        $client = Gemini::client(config('gemini.api_key'));
        $result = $client->generativeModel("models/gemini-3-flash")
            ->withGenerationConfig($generationConfig)
            ->generateContent($text)
            ->text();
        $token = $client->generativeModel("models/gemini-3-flash")
            ->countTokens($text)
            ->totalTokens;

        return [$result, $token];
    }
}
