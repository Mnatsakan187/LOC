<?php

namespace App\Http\Controllers;

use App\Mailshake;
use App\User;
use Facebook\WebDriver\Exception\UnknownServerException;
use Illuminate\Http\Request;
use Jhoule\Mailshake\Facades\Recipients;

class MailshakeController extends Controller
{
    public function index(){

        $mailshakesAPI =  Mailshake::getcampaigns();
        $mailshakesAPI = json_decode($mailshakesAPI);
        if(isset($mailshakesAPI->results)){
            foreach ($mailshakesAPI->results as $item) {
                $mailshakes = Mailshake::where('campaign_id',$item->id)->first();
                if(!isset($mailshakes->id)){
                    Mailshake::create([
                        'campaign_id' => $item->id,
                        'title' => trim($item->title)
                    ]);
                }
            }
            return response()->json(['success' => 'Synchronization with Mailshake successfully done'], 200);

        }
        else{
            return response()->json(['error' => 'Error in Synchronization with Mailshake, Please try again.'], 500);
        }
    }


    public function sendMailshake(Request $request){

        $mailShakeSetCampaignRecepientsArray = [];
        $mailShakeCampaignIDs = Mailshake::pluck('campaign_id')->toArray();
        // Loop mailshake campaigns
        foreach ($mailShakeCampaignIDs as $mailShakeCampaignId){
            $campaignRecepients = [];
            // Loop all updated subscriptions
                $campaignRecepients[] = [
                        'emailAddress'=>$request->email,
                        'fullName'=>$request->firstName. ' '. $request->lastName,
                        "fields"=>["addonName"=>'LOC']
                    ];

                    $mailShakeSetCampaignRecepientsArray[$mailShakeCampaignId] = [
                        'campaignID' => $mailShakeCampaignId,
                        'addAsNewList' => true,
                        'addresses' => $campaignRecepients
                    ];

        }

        foreach ($mailShakeSetCampaignRecepientsArray as $mailshakeCampaignWithRecipients){
           if($mailshakeCampaignWithRecipients['campaignID'] == 295353) {
               Mailshake::runcroncampaign($mailshakeCampaignWithRecipients);
           }

        }

    }
}
