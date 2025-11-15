<?php

namespace App\Http\Controllers;

use App\Models\AppConfiguration;
use App\Models\Donation\Booking;
use App\Models\Donation\Donation;
use App\Models\Heroes\Hero;
use App\Traits\BotDonationTrait;
use App\Traits\BotHeroTrait;
use App\Traits\BotVolunteerTrait;
use App\Traits\SendWhatsapp;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{
    use BotVolunteerTrait, BotHeroTrait, BotDonationTrait, SendWhatsapp;
    public function fromFonnte()
    {
        header('Content-Type: application/json; charset=utf-8');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $sender = $data['sender'];
        $message = $data['message'];
        $group = explode(',', AppConfiguration::getGroupCode());
        if (in_array($sender, $group)) {
            if ($message == '@BOT donasi hari ini') {
                $this->getActiveDonation($sender);
            } elseif ($message == '@all') {
                $this->mentionAll($sender);
            } elseif ($message == '@BOT status') {
                $this->send($sender, "ID GROUP : $sender");
            } elseif (str_starts_with($message, '@BOT')) {
                $this->send($sender, 'SEDANG DALAM PERBAIKAN');
            }
            // if ($message == '@BOT donasi hari ini') {
            //     $this->getActiveDonation($sender);
            // } elseif ($message == '@BOT hero hari ini') {
            //     $this->getAllActiveHero($sender);
            // } elseif ($message == '@BOT list donatur') {
            //     $this->getSponsorList($sender);
            // } elseif ($message == '@BOT list kontribusi') {
            //     $this->getRecap();
            // } elseif ($message == '@BOT hero yang belum') {
            //     $this->getAllNotYetHero($sender);
            // } elseif ($message == '@BOT ingatkan hero hari ini') {
            //     $this->reminderToday($sender);
            // } elseif ($message == '@all') {
            //     $this->mentionAll($sender);
            // } elseif (str_starts_with($message, '@BOT pemkot')) {
            //     $this->sendToPemkot($data);
            // } elseif (str_starts_with($message, '@BOT ingatkan hero yang belum')) {
            //     $this->reminderLastCall($message, $sender);
            // } elseif (str_starts_with($message, '@BOT laporan bulanan')) {
            //     $this->createMonthly($sender, $message);
            // } elseif (str_starts_with($message, '@BOT balas')) {
            //     $this->replyHero($sender, $message);
            // } elseif (str_starts_with($message, '@BOT dokumentasi')) {
            //     $this->giveDocumentation($message);
            // } elseif (str_starts_with($message, '@BOT avail')) {
            //     $this->getAvailableVolunteer($sender, $message);
            // } elseif (str_starts_with($message, '@BOT status')) {
            //     $this->getStatus($sender, $message);
            // }
        } else {
            $this->getReplyFromStranger($sender, $message);
        }
    }

    public function getReplyFromStranger($sender, $text)
    {
        $activeDonation = Donation::where('status', 'aktif')->pluck('id');
        $hero = Hero::where('phone', $sender)->where('status', 'belum')->whereIn('donation_id', $activeDonation)->first();
        $foodDonator = Booking::where('phone', $sender)->where('status', 'waiting')->first();
        if (str_starts_with($text, "verify")) {
            return true;
        }
        if ($hero) {
            $this->getReplyFromHeroes($hero, $text);
        } elseif ($foodDonator) {
            $this->getReplyFromFoodDonator($foodDonator, $text);
        } else {
            // $this->gemini($sender, $text);
        }
        return true;
    }

    public static function sendForPublic($target, $message, $from = 'FIRST')
    {
        if (!str_ends_with($target, '@g.us')) {
            return true;
        }
        Http::post(env('WHATSAPP_ENDPOINT', 'https://api.fonnte.com') . '/send', [
            'target' => $target,
            'message' => $message,
        ]);
    }
    // public static function sendForPublic($target, $message, $from)
    // {
    //     $curl = curl_init();

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
    //             'Authorization: ' . env("WHATSAPP_FONNTE_" . $from),
    //         ],
    //     ]);

    //     curl_exec($curl);
    //     curl_close($curl);
    // }
}
