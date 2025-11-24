<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Country;
use App\Models\Operator;
use Illuminate\Routing\Controller;

final class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::query()->where('is_active', true)
            ->with(['operators' => function ($query): void {
                $query->where('is_active', true);
            }])
            ->get();

        return response()->json($countries);
    }

    public function operators($countryId)
    {
        $operators = Operator::query()->where('country_id', $countryId)
            ->where('is_active', true)
            ->get();

        return response()->json($operators);
    }
}
