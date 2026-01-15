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
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{
    use BotDonationTrait, BotHeroTrait, BotVolunteerTrait, SendWhatsapp;

    public function fromFonnte()
    {
        header('Content-Type: application/json; charset=utf-8');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $sender = $data['sender'];
        $message = $data['message'];
        $media = $data['media'];
        if ($media) {
            dispatch(function () use ($data) {
                $this->handleMedia($data['media']);
            });
        }
        // $group = explode(',', AppConfiguration::getGroupCode());
        if (str_ends_with($sender, '@g.us')) {
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
                $this->mentionAll($data['sender']);
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
            } elseif (str_starts_with($message, '@BOT status')) {
                $this->getStatus($sender, $message);
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
            $this->getReplyFromHeroes($hero, $text);
        } elseif ($volunteer) {
            $this->getReplyFromVolunteer($volunteer, $text, $media);
        }
        return true;
    }

    public static function sendForPublic($target, $message, $from = 'FIRST')
    {
        Http::post(AppConfiguration::getWhatsAppEndpoint() . '/send', [
            'target' => $target,
            'message' => $message,
        ]);
    }
    // public static function sendForPublic($target, $message, $from = 'FIRST')
    // {
    //     $curl = curl_init();
    //     if (!str_ends_with($target, '@g.us')) {
    //         $from = 'SECOND';
    //     }
    //     $token = AppConfiguration::where('key', "FONNTE_$from")->first()->value;

    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => 'https://api.fonnte.com/send',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => [
    //             'target' => $target,
    //             'message' => $message,
    //             'schedule' => 0,
    //             'typing' => false,
    //             'delay' => '2',
    //             'countryCode' => '62',
    //         ],
    //         CURLOPT_HTTPHEADER => [
    //             'Authorization: ' . $token,
    //         ],
    //     ]);

    //     curl_exec($curl);
    //     curl_close($curl);
    // }
}
