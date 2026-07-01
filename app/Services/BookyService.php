<?php

namespace App\Services;

use App\Models\User;
use App\Models\KomunitasChat;
use App\Models\BookyChat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookyService
{
    protected $bookyUser;

    public function __construct()
    {
        $this->bookyUser = User::where('email', 'booky@bookverse.ai')->first();
    }

    /**
     * Handle mention in Community Chat
     */
    public function handleCommunityMention(KomunitasChat $chat)
    {
        if (!$this->bookyUser) return;
        
        // Remove @Booky from the message so the AI doesn't get confused
        $messageText = trim(str_ireplace('@Booky', '', $chat->pesan));
        
        if (empty($messageText)) {
            $messageText = "Sapa aku dengan riang"; // fallback
        }

        $reply = $this->askAi($messageText, 'community');

        $replies = explode('|||', $reply);
        
        foreach ($replies as $r) {
            $text = trim($r);
            if (!empty($text)) {
                KomunitasChat::create([
                    'komunitas_id' => $chat->komunitas_id,
                    'user_id' => $this->bookyUser->id,
                    'parent_id' => $chat->id, // reply directly to the user's chat
                    'pesan' => $text,
                    'media_path' => null,
                    'media_type' => null,
                ]);
            }
        }
    }

    /**
     * Handle Private Chat
     */
    public function handlePrivateChat(int $userId, string $message, ?int $parentId = null)
    {
        if (!$this->bookyUser) return;

        // Save user message first
        $userChat = BookyChat::create([
            'user_id' => $userId,
            'parent_id' => $parentId,
            'is_bot' => false,
            'pesan' => $message,
        ]);

        // Ask AI
        $reply = $this->askAi($message, 'private', $parentId);

        // Split multiple messages if AI uses ||| separator
        $replies = explode('|||', $reply);
        
        foreach ($replies as $r) {
            $text = trim($r);
            if (!empty($text)) {
                BookyChat::create([
                    'user_id' => $userId,
                    'parent_id' => $userChat->id, // AI replies to the user's message
                    'is_bot' => true,
                    'pesan' => $text,
                ]);
            }
        }
    }

    /**
     * Send prompt to Gemini API
     */
    protected function askAi(string $message, string $context, ?int $parentId = null): string
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            return "Maaf, otak jeniusku sedang tidur karena API Key belum dipasang di sistem. Silakan minta Admin untuk menambahkan `GEMINI_API_KEY` di file `.env` ya!";
        }

        $parentContext = "";
        if ($parentId) {
            $parentChat = BookyChat::find($parentId);
            if ($parentChat) {
                $parentContext = "Pengguna sedang membalas pesan ini: \"" . $parentChat->pesan . "\". Pastikan jawabanmu nyambung dengan konteks balasan tersebut.\n";
            }
        }

        $systemPrompt = "Kamu adalah Booky, asisten AI super ramah di aplikasi baca buku BookVerse. Saat ini adalah tahun " . date('Y') . ".
Aturan penting:
1. Gunakan bahasa Indonesia kekinian yang SANGAT ramah, antusias, dan hangat.
2. Wajib menggunakan BANYAK emoji yang bervariasi agar terlihat ekspresif.
3. Karena pengguna ingin pengalaman seperti sedang di-chat panjang, kamu WAJIB memecah jawabanmu menjadi beberapa pesan (minimal 2 atau 3 pesan). 
PISAHKAN setiap pesan dengan teks `|||`. Contoh: Wah halo kak! Senang banget disapa! 😊 ||| Btw kakak lagi nyari buku apa nih? 📚✨
Pesan ini datang dari: " . ($context == 'community' ? 'Grup Komunitas' : 'Chat Pribadi') . ".
" . $parentContext;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        try {
            $response = Http::post($url, [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemPrompt]
                    ]
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $message]
                        ]
                    ]
                ],
                'tools' => [
                    [
                        'googleSearch' => (object)[]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, aku sedang pusing memikirkan buku. Bisa ulangi?";
            }
            
            return "Maaf, sepertinya server otakkku sedang down. (Error: " . $response->status() . ")";

        } catch (\Exception $e) {
            Log::error('Booky AI Error: ' . $e->getMessage());
            return "Waduh, koneksiku ke otak pusat terputus. Coba lagi nanti ya!";
        }
    }
}
