<?php

namespace App\Traits;

use App\Models\Donation\Donation;
use Carbon\Carbon;

trait DashboardAnalytics
{
    public function getDonationAnalytics(int $monthsBack)
    {
        $currentDate = Carbon::now();
        $startDate = Carbon::parse(Carbon::now()->format('Y-m'))->subMonths($monthsBack);

        $donations = Donation::where('charity', 0)
            ->whereBetween('take', [$startDate, $currentDate])
            ->with(['foods', 'heroes'])
            ->get();

        $groupedData = $donations->groupBy(function ($donation) {
            return Carbon::parse($donation->take)->format('Y-m');
        });

        $lastData = [];
        foreach ($groupedData as $key => $item) {
            $hero_count = 0;
            $food_count = 0;

            foreach ($item as $data) {
                $hero_count += $data->heroes->sum('quantity');
                $food_count += $data->foods->where('expired', '0')->sum('weight') / 1000;
            }
            $lastData[] = [
                'bulan' => Carbon::parse($key)->format('F'),
                'heroes' => $hero_count,
                'foods' => $food_count,
            ];
        }

        return $lastData;
    }
}
