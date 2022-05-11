<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BkashController extends Controller
{

    protected $createURL = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/create";
    protected $executeURL = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/execute/";
    protected $tokenURL = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant";
    protected $script = "https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js";
    protected $proxy = "";
    protected $app_key = ""; //App key provided by bkash
    protected $app_secret = ""; //App secret provided by bkash
    protected $username = ""; //User name provided by bkash
    protected $password = ""; //password provided by bkash
    protected $token = "";

    /**
     *Generate bkash token
     *
     *@param \Illuminate\Http\Request $request
     *@return \Illuminate\Http\JsonResponse
     */
    public function bkashPaymentToken(Request $request)
    {
        try {

            $post_token = array(
                'app_key' => $this->app_key,
                'app_secret' => $this->app_secret
            );

            $url = curl_init($this->tokenURL);

            $posttoken = json_encode($post_token);
            $header = array(
                'Content-Type:application/json',
                'password:' . $this->password,
                'username:' . $this->username
            );

            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($url, CURLOPT_PROXY, $proxy);
            $resultdata = curl_exec($url);

            curl_close($url);


            $token = json_decode($resultdata, true);

            $data = [];
            $data['authToken'] = $token;
            $data['currency'] = "BDT";
            $data['intent'] = 'sale';

            return response()->json(
                [
                    'result' => $data,
                    'success' => true
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Create bkash payment
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createpayment(Request $request)
    {
        $token = $request->token;

        $amount = $request->amount;
        $invoice = $request->merchantInvoiceNumber; // must be unique
        $intent = 'sale';

        $createpaybody = array('amount' => $amount, 'currency' => 'BDT', 'merchantInvoiceNumber' => $invoice, 'intent' => $intent);
        $url = curl_init($this->createURL);

        $createpaybodyx = json_encode($createpaybody);

        $header = array(
            'Content-Type:application/json',
            'authorization:' . $token,
            'x-app-key:' . $this->app_key
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $createpaybodyx);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);

        $resultdata = curl_exec($url);
        $data = json_decode($resultdata);
        curl_close($url);
        return response()->json(
            [
                'success' => true,
                'data' => $data
            ]
        );
    }

    /**
     * Execute bkash payment
     * 
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function executepayment(Request $request)
    {
        try {

            $paymentID = $request->paymentID;

            $url = curl_init($this->executeURL . $paymentID);

            $header = array(
                'Content-Type:application/json',
                'authorization:' . $request->token,
                'x-app-key:' . $this->app_key
            );

            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            // curl_setopt($url, CURLOPT_PROXY, $proxy);

            $resultdatax = curl_exec($url);
            $data = json_decode($resultdatax);
            curl_close($url);

            return response()->json(
                [
                    'success' => true,
                    'data' => $data,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => $data,
                ]
            );
        }
    }
}
