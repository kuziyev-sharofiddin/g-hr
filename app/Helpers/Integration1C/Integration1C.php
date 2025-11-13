<?php

namespace App\Helpers\Integration1C;

use Illuminate\Support\Facades\Http;

class Integration1C
{
    // private static $base_url = "http://192.168.10.93:80/Garant/hs/IntegrationWithMobileApplications/Basic/";
    // private static $authorization = 'Basic TW9iaWxlQXBwOk1vYmlsZUFwcDE=';
    // private static $base_url = "http://10.100.109.102:8080/Garant4/hs/IntegrationWithMobileApplications/Basic/";
    // private static $authorization = 'Basic TW9iaWxlQXBwOk1vYmlsZUFwcDE=';

    //get ticket
    public static function getTicket()
    {
        $data = [
            "token" => "m4MC0ck4Ku7Ul4L2hHy9Yj3Jx9Xi3IQq6tT7l4Lw"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('GENERAL_BASE_URL') . "GetTicket/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: ' . env('GENERAL_AUTHORIZATION')]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
        $result = curl_exec($ch);
        $ticket = json_decode($result)->massage->ticket;

        return $ticket;
    }

    //get data
    public static function getData($data, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('GENERAL_BASE_URL') . $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Javob kutish vaqti
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // Ulanish kutish vaqti
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: ' . env('GENERAL_AUTHORIZATION')]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
        $result = curl_exec($ch);
        $result = json_decode($result);

        return $result;
    }

    public static function sendRequest($data, $url)
    {
        $fullUrl = env('GENERAL_BASE_URL') . $url;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => env('GENERAL_AUTHORIZATION'),
        ])->retry(3, 60000, function ($exception, $request) use ($fullUrl, $data) {
            // Xatolik yoki muvaffaqiyatsizlik (success = false) holatida qayta urinish
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => env('GENERAL_AUTHORIZATION'),
            ])->post($fullUrl, $data);

            $result = $response->json();

            return !$result['success']; // success false bo‘lsa qayta urinadi
        })->timeout(120)->post($fullUrl, $data);

        return $response->json(); // JSON ko‘rinishida natija
    }


    // // get selery

    // //get ticket
    // public static function getTicketSalary()
    // {
    //     $data = [
    //         "token" => "m4MC0ck4Ku7Ul4L2hHy9Yj3Jx9Xi3IQq6tT7l4Lw"
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, self::$base_url_asosiy . "GetTicket/");
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', self::$authorization]);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
    //     $result = curl_exec($ch);
    //     $ticket = json_decode($result)->massage->ticket;

    //     return $ticket;
    // }

    // //get data
    // public static function getDataSalary($data, $url)
    // {
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, self::$base_url_asosiy . $url);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', self::$authorization]);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
    //     $result = curl_exec($ch);
    //     $result = json_decode($result);

    //     return $result;
    // }

    public static function apiPostRequest($data, $url, $generalBaseUrl, $generalAuhorization)
    {
        $fullUrl = $generalBaseUrl . $url;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $generalAuhorization,
        ])->retry(3, 60000, function ($exception, $request) use ($fullUrl, $data, $generalAuhorization) {
            // Xatolik yoki muvaffaqiyatsizlik (success = false) holatida qayta urinish
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $generalAuhorization,
            ])->post($fullUrl, $data);

            $result = $response->json();

            return !$result['success']; // success false bo‘lsa qayta urinadi
        })->timeout(120)->post($fullUrl, $data);

        return $response->json(); // JSON ko‘rinishida natija
    }

    public static function apiGetTicket($token, $generalBaseUrl, $generalAuthorization, $url)
    {
        $data = [
            "token" => $token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $generalBaseUrl . $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: ' . $generalAuthorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
        $result = curl_exec($ch);

        $ticket = json_decode($result)->massage->ticket;

        return $ticket;
    }
}
