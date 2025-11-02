<?php

namespace App\Console\Commands;

use App\Http\Controllers\BotController;
use App\Models\AppConfiguration;
use App\Models\Volunteer\User;
use App\Traits\SendWhatsapp;
use DateTime;
use DateTimeZone;
use Google\Client;
use Google\Service\Calendar;
use Illuminate\Console\Command;

class CalendarNotification extends Command
{
    use SendWhatsapp;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:send-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send calendar notification for event at tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $client = new Client();
            $client->setClientId($_ENV['GOOGLE_CALENDAR_CLIENT_ID']);
            $client->setClientSecret($_ENV['GOOGLE_CALENDAR_CLIENT_SECRET']);
            $client->refreshToken($_ENV['GOOGLE_CALENDAR_REFRESH_TOKEN']);
            $delay = 10;

            $client->addScope(Calendar::CALENDAR_READONLY);
            $client->addScope(Calendar::CALENDAR_ACLS_READONLY);
            $service = new Calendar($client);
            $result = [];
            $calendarList = $service->calendarList->listCalendarList();
            $tomorrowStart = new DateTime('tomorrow 00:00:00');
            $tomorrowEnd   = new DateTime('tomorrow 23:59:59');
            foreach ($calendarList->getItems() as $calendar) {
                $calendarId = $calendar->getId();

                $emails = [];
                $acls = $service->acl->listAcl($calendarId);
                foreach ($acls->getItems() as $rule) {
                    $scope = $rule->getScope();
                    if ($scope->type === 'user' && !empty($scope->value)) {
                        $emails[] = $scope->value;
                    }
                }

                $events = $service->events->listEvents($calendarId, [
                    'timeMin' => $tomorrowStart->format(DateTime::RFC3339),
                    'timeMax' => $tomorrowEnd->format(DateTime::RFC3339),
                    'orderBy' => 'startTime',
                    'singleEvents' => true,
                    'maxResults' => 100
                ]);

                foreach ($events->getItems() as $event) {
                    $start = $event->start->dateTime ?? $event->start->date;
                    $end   = $event->end->dateTime ?? $event->end->date;

                    $startDT = new DateTime($start);
                    $startDT->setTimezone(new DateTimeZone('Asia/Jakarta'));

                    $endDT = new DateTime($end);
                    $endDT->setTimezone(new DateTimeZone('Asia/Jakarta'));

                    // Format tanggal dalam bahasa Indonesia
                    \Carbon\Carbon::setLocale('id');
                    $tanggalIndo = \Carbon\Carbon::parse($startDT)->translatedFormat('j F Y');

                    if ($event->start->dateTime) {
                        $jamMulai   = $startDT->format('H:i');
                        $jamSelesai = $endDT->format('H:i');
                        $waktu = "{$jamMulai} - {$jamSelesai} WIB";
                    } else {
                        $waktu = "Sepanjang Hari";
                    }

                    $message = "📢 Pengingat Acara!\n" .
                        "Judul   : " . $event->getSummary() . "\n" .
                        "Tanggal : {$tanggalIndo}\n" .
                        "Waktu   : {$waktu}";

                    $phones = User::whereIn('email', $emails)->pluck('phone')->toArray();

                    $result[] = [
                        "message" => $message,
                        "target"  => $phones
                    ];
                }
            }
            if (count($result)) {
                foreach ($result as $item) {
                    $message = $item["message"];
                    foreach ($item["target"] as $target) {
                        dispatch(function () use ($target, $message) {
                            BotController::sendForPublic($target, $message);
                        })->delay(now()->addSeconds($delay));
                        $delay = $delay + 10;
                    }
                }
            }
        } catch (\Throwable $th) {
            return BotController::sendForPublic('120363399651067268@g.us', "[ERROR]\n\nNeed GCALENDAR Refresh Token", AppConfiguration::useWhatsapp());
        }
    }
}
