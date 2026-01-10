<?php

namespace App\Http\Controllers;

use App\Models\Donation\Donation;
use App\Models\Donation\Sponsor;
use App\Models\Heroes\University;
use App\Traits\BotHeroTrait;
use App\Traits\BotVolunteerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    use BotHeroTrait, BotVolunteerTrait;

    public function index()
    {
        $donations = Donation::with('sponsor')->orderByRaw("CASE WHEN status = 'aktif' THEN 0 WHEN status = 'selesai' THEN 1 ELSE 2 END")
            ->orderBy('take')
            ->paginate(10);

        return view('pages.donation.index', compact('donations'));
    }

    public function charity()
    {
        $donations = Donation::where('charity', 1)->with('sponsor')->orderByRaw("CASE WHEN status = 'aktif' THEN 0 WHEN status = 'selesai' THEN 1 ELSE 2 END")
            ->orderBy('take')
            ->paginate(10);

        return view('pages.donation.index', compact('donations'));
    }

    public function rescue()
    {
        $donations = Donation::where('charity', 0)->with('sponsor')->orderByRaw("CASE WHEN status = 'aktif' THEN 0 WHEN status = 'selesai' THEN 1 ELSE 2 END")
            ->orderBy('take')
            ->paginate(10);

        return view('pages.donation.index', compact('donations'));
    }

    public function create()
    {
        $sponsors = Sponsor::all();
        $universities = University::all();

        return view('pages.donation.create', compact('sponsors', 'universities'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token', 'notify');
        if ($request->has('charity')) {
            $data['charity'] = true;
        }
        $data['remain'] = $request->quota;
        $data['status'] = 'aktif';
        $data['beneficiaries'] = json_encode($request->beneficiaries);
        if ($data['beneficiaries'] == 'null') {
            return back()->with('error', 'Pilih minimal satu beneficiaries');
        }

        $donation = Donation::create($data);
        if ($request->duration) {
            $minutes = (int) $request->input('duration');
            $from = Carbon::createFromTime($donation->hour, $donation->minute);
            $until = $from->copy()->addMinutes($minutes);
            $take = $from->format('H:i').' - '.$until->format('H:i');
        } else {
            $take = str_pad($donation->hour, 2, '0', STR_PAD_LEFT).':'.str_pad($donation->minute, 2, '0', STR_PAD_LEFT);
        }

        if ($request->has('notify')) {
            $this->sendNotification($donation, $take);
        }
        $this->notificationForDocumentation($donation);

        return redirect()->route('donation.index')->with('success', 'Berhasil menambahkan donasi');
    }

    public function show(Donation $donation)
    {
        if ($donation->partner_id) {
            $heroes = $donation->partner->heroes()->with('faculty')->get();
            $donations = Donation::whereNot('id', $donation->id)->where('status', 'aktif')->orWhere('id', '=', $donation->partner_id)->get();
        } else {
            $donations = Donation::whereNot('id', $donation->id)->where('status', 'aktif')->get();
            $heroes = $donation->heroes()->with('faculty')->get();
        }
        $foods = $donation->foods;

        return view('pages.donation.show', compact('donation', 'foods', 'heroes', 'donations'));
    }

    public function edit(Donation $donation)
    {
        $universities = University::all();

        return view('pages.donation.edit', compact('donation', 'universities'));
    }

    public function update(Request $request, Donation $donation)
    {
        if ($request->notes || $request->partner_id != '') {
            $donation->notes = $request->notes;
            if ($request->partner_id != '') {
                $donation->partner_id = $request->partner_id;
            } else {
                $donation->partner_id = null;
            }
            $donation->save();

            return back()->with('success', 'Berhasil mengubah data partner');
        }
        $donation->take = $request->take;
        $donation->status = $request->status;
        $donation->hour = $request->hour;
        $donation->minute = $request->minute;
        $donation->message = $request->message;

        if ($request->add) {
            $donation->remain = $donation->remain + $request->add;
            $donation->quota = $donation->quota + $request->add;
        }
        if ($request->diff) {
            $donation->remain = $donation->remain - $request->diff;
            $donation->quota = $donation->quota - $request->diff;
        }
        $beneficiaries = json_encode($request->beneficiaries);
        if ($beneficiaries == 'null') {
            $beneficiaries = null;
        }
        $donation->beneficiaries = $beneficiaries;

        if ($donation->status == 'selesai') {
            if ($donation->partner_id == null) {
                $foods = $donation->foods->sum('weight');
                $partners = $donation->partners;
                if ($partners->count() > 0) {
                    foreach ($partners as $partner) {
                        $foods += $partner->foods->sum('weight');
                    }
                }
                $heroes = $donation->heroes;
                if ($heroes->count() > 0) {
                    $foods = $foods / $heroes->sum('quantity');
                    foreach ($heroes as $hero) {
                        $hero->weight = $foods * $hero->quantity;
                        $hero->save();
                    }
                }
            }
            $donation->quota = $donation->quota - $donation->remain;
            $donation->remain = 0;
        }
        $donation->charity = $request->has('charity');
        $donation->save();

        return redirect()->route('donation.index')->with('success', 'Berhasil mengubah data donasi');
    }

    public function destroy(Donation $donation)
    {
        if ($donation->heroes->count() == 0 && $donation->foods->count() == 0) {
            $donation->delete();

            return redirect()->route('donation.index')->with('success', 'Berhasil menghapus donasi');
        }

        return redirect()->route('donation.index')->with('error', 'Gagal menghapus donasi');
    }

    public function gallery()
    {
        $donations = Donation::whereNotNull('media')
            ->where('media', '!=', '')
            ->where('media', 'LIKE', '%drive.google.com%')
            ->with(['sponsor', 'foods'])
            ->latest()
            ->paginate(12);

        return view('pages.donation.gallery', compact('donations'));
    }

    public static function extractFolderId($url)
    {
        if (preg_match('/folders\/([a-zA-Z0-9-_]+)/', $url, $match)) {
            return $match[1];
        }
        if (preg_match('/id=([a-zA-Z0-9-_]+)/', $url, $match)) {
            return $match[1];
        }

        return $url;
    }
}
