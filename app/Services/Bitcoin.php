<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Bitcoin
{
    public static function getUsdPrice(): float
    {
        $fmpKey = "W52QFnXt2v2xIDXFmlUHnQ3EWwlMncbl";
        $url = "https://financialmodelingprep.com/api/v3/quote/BTCUSD?apikey={$fmpKey}";
        $response = Http::get($url)->json();
        $price = $response[0]['price'];
        return $price;
    }
}
