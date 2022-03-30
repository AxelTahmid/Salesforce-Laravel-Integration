<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SalesForceCustom extends Controller
{
    protected $access_token;
    protected $instance_url;

    // generates a bearer token for salesforce OAuth, Session has been used
    // to reduce response time drastically. It will save token for 1h, only
    // first call will take 5-6 seconds, rest 0.1 seconds 
    public function getToken()
    {

        $time = Session::get('sf_auth_token_time');
        $currentTime = Carbon::now()->format('Y-m-d H:i:s');

        if ($currentTime > $time) {
            // asForm sends it as x-www-form-urlencoded
            $response = Http::asForm()->post(env('SF_LOGIN_URL') . '/services/oauth2/token', [
                'grant_type' =>  env('SF_AUTH_METHOD'), // "password" in this case
                'client_id' =>  env('SF_CLIENT_ID'),
                'client_secret' => env('SF_CLIENT_SECRET'),
                'username' =>   env('SF_USERNAME'),
                'password' => env('SF_PASSWORD'),
            ]);

            // guzzle built in for status code >= 400
            if ($response->failed()) {
                return response($response, 400);
            }

            $this->access_token = $response['access_token'];
            $this->instance_url = $response['instance_url'];

            // keeps token for 60 minutes
            Session::put('sf_auth_token_time', Carbon::now()->addMinutes(60)->format('Y-m-d H:i:s'));
            Session::put('sf_access_token', $this->access_token);
            Session::put('sf_instance_url', $this->instance_url);
        } else {
            $this->access_token = Session::get('sf_access_token');
            $this->instance_url = Session::get('sf_instance_url');
        }

        return response()->json(['access_token' => $this->access_token, 'instance_url' => $this->instance_url]);
    }


    public function test()
    {
        $partnerOneRecordId = '012090000008hVwAAI';


        $data = [];

        $data["RecordTypeId"] =   $partnerOneRecordId;
        $data["FirstName"] = 'Test';
        $data["LastName"] = ' 5';
        $data["Email__c"] = 'test.05@tikweb.com';
        $data["Wedding_date__c"] = '2023-08-03';
        $data["Country__c"] = 'SE';
        $data["CurrencyIsoCode"] = 'SEK'; // use the currency validator function created before
        $data["Preferred_language__pc"] = 'de';
        $data["Planner_App_Token__c"] = '253925'; // app token

        $this->getToken();

        $response = Http::acceptJson()
            ->withToken($this->access_token)
            ->post($this->instance_url . '/services/data/v53.0/sobjects/Account', $data);

        if ($response->failed()) {
            return response($response, 400);
        } else {
            return response($response->json(), 201);
        }
    }

    // for salesforce marketing cloud
    public function sfmcToken()
    {
        $response = Http::post('https://' . env('SFMC_SUBDOMAIN') . '.auth.marketingcloudapis.com/v2/token', [
            'grant_type' => env('SFMC_GRANT_TYPE'),
            'client_id' =>  env('SFMC_CLIENT_ID'),
            'client_secret' =>  env('SFMC_CLIENT_SECRET'),
            'account_id' =>   env('SFMC_ACCOUNT_ID'),
        ]);

        return $response->json();
    }

    // salesforce marketing cloud trigger
    public function sendResetEmail()
    {
        $token = $this->sfmcToken();

        $host = 'hostname';
        $eventDefKey = 'eventdefkey';

        $body = [];
        $body['ContactKey'] = 'test.something@gmail.com';
        $body['EventDefinitionKey'] = $eventDefKey;

        $data = [];
        $data['resetLink'] = 'https://something.something';
        $data['email'] = 'test.tikweb.1@gmail.com';
        $data['market'] = 'Sweden';
        $data['language'] = 'SE';
        $data['code'] = '454588';


        $body['Data'] = $data;


        $response = Http::acceptJson()
            ->withToken($token['access_token'])
            ->post($host . '/interaction/v1/events', $body);

        return $response->json();
    }
}
