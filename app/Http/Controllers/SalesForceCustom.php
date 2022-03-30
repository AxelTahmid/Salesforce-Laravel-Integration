<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SalesForceCustom extends Controller
{
    protected $access_token;
    protected $instance_url;

    public function getToken()

    {
        $response = Http::asForm()->post('https://login.salesforce.com/services/oauth2/token', [
            'grant_type' =>  env('SF_AUTH_METHOD'),
            'client_id' =>  env('SF_CLIENT_ID'),
            'client_secret' => env('SF_CLIENT_SECRET'),
            'username' =>   env('SF_USERNAME'),
            'password' => env('SF_PASSWORD'),
        ]);

        if ($response->failed()) {
            return response($response, 400);
        } else {
            $this->access_token = $response['access_token'];
            $this->instance_url = $response['instance_url'];

            return response('access_token created', 201);
        }
    }

    public function test()
    {

        $partnerOneRecordId = '012090000008hVwAAI';


        $data = [];

        $data["RecordTypeId"] =   $partnerOneRecordId;
        $data["FirstName"] = 'Test 2';
        $data["LastName"] = 'ContactID';
        $data["Email__c"] = 'test.contactID02@test.com';
        $data["Wedding_date__c"] = '2023-08-03';
        $data["Country__c"] = 'SE';
        $data["CurrencyIsoCode"] = 'SEK'; // use the currency validator function created before
        $data["Preferred_language__pc"] = 'de';
        $data["Planner_App_Token__c"] = '253925'; // app token

        $this->getToken();

        $response = Http::acceptJson()->withToken($this->access_token)->post($this->instance_url . '/services/data/v53.0/sobjects/Account', $data);

        return $response;
    }


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

    public function sendResetEmail()
    {
        $token = $this->sfmcToken();

        $host = 'hostname';

        $eventDefKey = 'eventdefkey';

        $body = [];
        $data = [];

        $body['ContactKey'] = 'test.something@gmail.com';
        $body['EventDefinitionKey'] = $eventDefKey;

        $data['resetLink'] = 'https://something.something';
        $data['email'] = 'test.tikweb.1@gmail.com';
        $data['market'] = 'Sweden';
        $data['language'] = 'SE';
        $data['code'] = '454588';


        $body['Data'] = $data;


        $response = Http::withToken(
            $token['access_token']
        )->post($host . '/interaction/v1/events', $body);

        return $response->json();
    }
}
