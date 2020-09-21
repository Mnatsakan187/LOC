<?php


namespace App\Http\Controllers;


use App\Subscription;
use App\User;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;

class StripePaymentController
{
    /**
     * success response method.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UserAccountTypeException
     */
    public function stripePost(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        Charge::create ([
             "amount" => $request->stripe['amount'],
             "currency" => "USD",
             "source" => $request->stripe['stripeToken']['id'],
             "description" => "Test payment from LOC"
        ]);

        if(isset($request->userId) && !empty($request->userId)) {

            /** @var User $user */
            $user = User::query()->findOrFail($request->userId);

            $subscription = $user->subscriptions()->firstOrCreate([
                'plan_id' => $request['stripe']['plan']
            ]);

            if ($subscription) {
                $user->updateAccountTypeTo($user::ACCOUNT_TYPE_CREATOR);
            }
        }

        return response()->json(['success'],201);;
    }
}
