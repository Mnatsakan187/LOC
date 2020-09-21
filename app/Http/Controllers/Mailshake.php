<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mailshake extends Model
{
    protected $table = 'mailshakes';

    protected $fillable = [
        'campaign_id',
        'title'
    ];


    public static function getcampaigns(){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('services.mailshake.url').'campaigns/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
                'authorization: '.config('services.mailshake.api_key'),
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }

    public static function runcampaign($campaignid, $subscriptions_name, $subscriptions_email, $addonName){

        $post = [];
        for ($i=0; $i<count($subscriptions_email); $i++){
            $post[] = [
                "emailAddress"=>$subscriptions_email[$i],
                "fullName"=>$subscriptions_name[$i],
                "fields"=>["addonName"=>$addonName[$i]]
            ];
        }

        $CURLOPT_POSTFIELDS = [
            "campaignID"=>295342,
            "addAsNewList"=> true,
            "addresses"=> $post
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('services.mailshake.url').'recipients/add',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'authorization: '.config('services.mailshake.api_key')
            ),
            CURLOPT_POSTFIELDS =>  http_build_query($CURLOPT_POSTFIELDS)
        ));
        $response = curl_exec($curl);

        dd($response);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return $err;
        } else {
            return $response;
        }

    }

    public static function runcroncampaign($CURLOPT_POSTFIELDS){

        $curl = curl_init();
        $action = config('services.mailshake.url').'recipients/add';
        $method = "POST";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $action,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'authorization: '.config('services.mailshake.api_key')
            ),
            CURLOPT_POSTFIELDS =>  http_build_query($CURLOPT_POSTFIELDS)
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            print_r($err);

            return $err;
        } else {
            return $response;
        }

    }
}
