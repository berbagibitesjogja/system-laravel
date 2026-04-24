<?php

namespace App\Http\Controllers;

use App\Models\AppConfiguration;
use App\Models\Donation\Booking;
use App\Models\Donation\Donation;
use App\Models\Heroes\Hero;
use App\Models\Volunteer\User;
use App\Traits\BotDonationTrait;
use App\Traits\BotHeroTrait;
use App\Traits\BotVolunteerTrait;
use App\Traits\SendWhatsapp;
use App\Traits\SendModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    use BotDonationTrait, BotHeroTrait, BotVolunteerTrait, SendWhatsapp, SendModel;

    public function fromFonnte(Request $request)
    {
        $data = $request->all();

        // fallback
        if (empty($data)) {
            $data = json_decode($request->getContent(), true);
        }

        // Log
        Log::info('Fonnte Webhook Incoming', [
            'all' => $request->all(),
            'raw' => $request->getContent(),
            'parsed' => $data,
        ]);
        $sender = $data['sender'] ?? null;
        $message = $data['message'] ?? null;
        $media = $data['url'] ?? null;
        $isGroup = $data['isgroup'] ?? false;

        // if ($media) {
        //     dispatch(function () use ($media) {
        //         $this->handleMedia($media);
        //     });
        // }

        // guard
        if (!$sender || !$message) {
            Log::warning('Any data got null', ['data' => $data]);
            return response()->json(['status' => 'ignored'], 200);
        }

        if ($message == '@BOT status') {
            $this->getStatus($sender, $message);
        }
        if ($isGroup) {
            if ($message == '@BOT donasi hari ini') {
                $this->getActiveDonation($sender);
            } elseif ($message == '@BOT hero hari ini') {
                $this->getAllActiveHero($sender);
            } elseif ($message == '@BOT list donatur') {
                $this->getSponsorList($sender);
            } elseif ($message == '@BOT list kontribusi') {
                $this->getRecap();
            } elseif ($message == '@BOT hero yang belum') {
                $this->getAllNotYetHero($sender);
            } elseif ($message == '@BOT ingatkan hero hari ini') {
                $this->reminderToday($sender);
            } elseif ($message == '@all') {
                $this->send($sender, 'Maaf sedang perbaikan');
                // $this->mentionAll($data['sender']);
            } elseif (str_starts_with($message, '@BOT pemkot')) {
                $this->sendToPemkot($data);
            } elseif (str_starts_with($message, '@BOT ingatkan hero yang belum')) {
                $this->reminderLastCall($message, $sender);
            } elseif (str_starts_with($message, '@BOT laporan bulanan')) {
                $this->createMonthly($sender, $message);
            } elseif (str_starts_with($message, '@BOT balas')) {
                $this->replyHero($sender, $message);
            } elseif (str_starts_with($message, '@BOT dokumentasi')) {
                $this->giveDocumentation($message);
            }
        } else {
            $this->getReplyFromPersonal($sender, $message, $media);
        }
    }

    public function getReplyFromPersonal($sender, $text, $media)
    {
        $activeDonation = Donation::where('status', 'aktif')->pluck('id');
        $hero = Hero::where('phone', $sender)->where('status', 'belum')->whereIn('donation_id', $activeDonation)->first();
        $volunteer = User::where('phone', $sender)->first();
        if (str_starts_with($text, '> Verify')) {
            return $this->verifyFoodHeroes($sender, $text);
        }
        if (str_starts_with($text, '> Verifikasi')) {
            return $this->verifyNotify($sender, $text);
        }
        if ($hero) {
            return $this->getReplyFromHeroes($hero, $text);
        } elseif ($volunteer) {

            return $this->getReplyFromVolunteer($volunteer, $text, $media);
        } else {
            $reply = $this->askModel($text);
            $this->send($sender, $reply);
        }
        return true;
    }

    // public static function sendForPublic($target, $message, $media = 'FIRST')
    // {
    //     if (in_array($media, ['FIRST', 'SECOND'])) {
    //         $media = null;
    //     }
    //     $token = AppConfiguration::where('key', "FONNTE_FIRST")->first()->value;
    //     Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $token,
    //     ])
    //         ->post(AppConfiguration::getWhatsAppEndpoint() . '/send', [
    //             'target'  => $target,
    //             'message' => $message,
    //             'media'   => $media,
    //         ]);
    // }
    public static function sendForPublic($target, $message, $media = 'FIRST')
    {
        $curl = curl_init();

        $token = AppConfiguration::where('key', "FONNTE_FIRST")->first()->value;

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
                'schedule' => 0,
                'typing' => false,
                'delay' => '2',
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $token,
            ],
        ]);

        curl_exec($curl);
        curl_close($curl);
    }

    public function getBotNumber()
    {
        $botNumber = AppConfiguration::getBotNumber();
        return response()->json(['bot_number' => $botNumber], 200);
    }
}
