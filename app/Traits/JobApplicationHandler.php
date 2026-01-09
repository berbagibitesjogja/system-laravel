<?php

namespace App\Traits;

use App\Models\FormJob;
use App\Models\Volunteer\Cancelation;
use App\Models\Volunteer\User;

trait JobApplicationHandler
{
    public function handleJobApplication(User $volunteer, $entry, $jobId)
    {
        if ($volunteer->cancelation?->banned) {
            $message = "Maaf kamu tidak bisa mendaftar hingga " . $volunteer->cancelation->banned;
            $messageS = $volunteer->name . " tidak bisa mendaftar karena terkena banned hingga " . $volunteer->cancelation->banned;
            dispatch(function () use ($volunteer, $message, $messageS) {
                $this->send($volunteer->phone, $message);
                $this->send("120363330280278639@g.us", $messageS);
            });
            return redirect()->away('https://berbagibitesjogja.com/war');
        }

        $apply = FormJob::whereId($entry)->first();

        if (!$apply) {
            return redirect()->away('https://berbagibitesjogja.com/war');
        }

        $data = collect($apply['data']);
        $jobs = collect($data->get('jobs'));
        $jobItemIndex = $jobs->search(fn($j) => $j['id'] == $jobId);

        if ($jobItemIndex === false) {
            return redirect()->away('https://berbagibitesjogja.com/war');
        }

        $jobItem = $jobs[$jobItemIndex];

        // Check division restriction
        if (!empty($jobItem['division']) && $jobItem['division'] != $volunteer->division->name) {
            return redirect()->away('https://berbagibitesjogja.com/war');
        }

        // Work with persons
        $persons = collect($jobItem['persons']);
        $alreadyExists = str_contains(json_encode($jobs), $volunteer->phone);

        if (!$alreadyExists && $persons->count() < $jobItem['need']) {
            // Add the volunteer
            $persons->push([
                'name' => $volunteer->name,
                'phone' => $volunteer->phone,
            ]);

            // Update persons in jobItem
            $jobItem['persons'] = $persons;

            // Update job in jobs list
            $jobs[$jobItemIndex] = $jobItem;

            // Save back to data
            $data['jobs'] = $jobs;

            // Save to DB
            $apply->data = $data;
            $apply->save();
            $message = $volunteer->name
                . " berhasil mendaftar"
                . "\n\nDonatur: " . $data->get('sponsor')
                . "\nPenerima: " . $data->get('receiver')
                . "\nTanggal: " . $data->get('date')
                . "\nTugas: " . $jobItem['name'];
            $messageV = "Kamu berhasil mendaftar"
                . "\n\nDonatur: " . $data->get('sponsor')
                . "\nPenerima: " . $data->get('receiver')
                . "\nTanggal: " . $data->get('date')
                . "\nTugas: " . $jobItem['name'];
            dispatch(function () use ($volunteer, $message, $messageV) {
                $this->send('120363330280278639@g.us', $message);
                $this->send($volunteer->phone, $messageV);
            });
        }
        return redirect()->away('https://berbagibitesjogja.com/war');
    }

    public function handleJobUnapplication(User $volunteer, $entry, $jobId)
    {
        $apply = FormJob::whereId($entry)->first();

        if (!$apply) {
            return redirect()->away('https://berbagibitesjogja.com/war');
        }

        $data = collect($apply['data']);
        $jobs = collect($data->get('jobs'));
        $jobItemIndex = $jobs->search(fn($j) => $j['id'] == $jobId);

        if ($jobItemIndex === false) {
            return redirect()->away('https://berbagibitesjogja.com/war');
        }

        $jobItem = $jobs[$jobItemIndex];

        // Work with persons
        $persons = collect($jobItem['persons']);

        // Remove the person (unapply)
        $updatedPersons = $persons->reject(function ($person) use ($volunteer) {
            return $person['phone'] === $volunteer->phone;
        })->values(); // reindex the array

        // Save only if there's a change
        if ($updatedPersons->count() !== $persons->count()) {
            $jobItem['persons'] = $updatedPersons;
            $jobs[$jobItemIndex] = $jobItem;
            $data['jobs'] = $jobs;
            $apply->data = $data;
            $apply->save();
        }
        if ($volunteer->role == 'member') {
            if ($volunteer->cancelation) {
                $volunteer->cancelation->tries += 1;
                if ($volunteer->cancelation->tries == 2) {
                    $volunteer->cancelation->banned = now()->addDays(14 * $volunteer->cancelation->tries);
                }
                $volunteer->cancelation->save();
                $message = "Halo kamu tidak bisa mendaftar lagi hingga " . $volunteer->cancelation->banned;
                dispatch(function () use ($volunteer, $message) {
                    $this->send($volunteer->phone, $message);
                });
            } else {
                Cancelation::create(['user_id' => $volunteer->id]);
                $message = "Halo kamu punya kesempatan 1 kali lagi sebelum terkena banned jika membatalkan lagi.";
                dispatch(function () use ($volunteer, $message) {
                    $this->send($volunteer->phone, $message);
                });
            }
        }
        $message = $volunteer->name
            . " membatalkan ikut"
            . "\n\nDonatur: " . $data->get('sponsor')
            . "\nPenerima: " . $data->get('receiver')
            . "\nTanggal: " . $data->get('date')
            . "\nTugas: " . $jobItem['name'];
        $messageV = "Kamu membatalkan"
            . "\n\nDonatur: " . $data->get('sponsor')
            . "\nPenerima: " . $data->get('receiver')
            . "\nTanggal: " . $data->get('date')
            . "\nTugas: " . $jobItem['name'];
        dispatch(function () use ($volunteer, $message, $messageV) {
            $this->send('120363350581821641@g.us', $message);
            $this->send($volunteer->phone, $messageV);
        });
        return redirect()->away('https://berbagibitesjogja.com/war');
    }
}
