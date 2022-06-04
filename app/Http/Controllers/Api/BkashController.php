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
    protected $app_key = "5tunt4masn6pv2hnvte1sb5n3j"; //App key provided by bkash
    protected $app_secret = "1vggbqd4hqk9g96o9rrrp2jftvek578v7d2bnerim12a87dbrrka"; //App secret provided by bkash
    protected $username = "sandboxTestUser"; //User name provided by bkash
    protected $password = "hWD@8vtzw0"; //password provided by bkash
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

            $request->session()->forget('bkash_token');
            if (array_key_exists('id_token', $token)) {
                $request->session()->put('bkash_token', $token['id_token']);
            }

            $request->session()->forget('amount');

            $request->session()->put('amount', $request->amount);

            $data = [];
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
        $token = $request->session()->get('bkash_token');
        $amount = $request->session()->get('amount');

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
            $token = $request->session()->get('bkash_token');
            $header = array(
                'Content-Type:application/json',
                'authorization:' . $token,
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
            $qp = $this->queryPayment($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $data,
                    'qp' => $qp
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
    /**
     * Bkash query payment
     * 
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryPayment($request)
    {
        $paymentID = $request->paymentID;
        $token = $request->session()->get('bkash_token');
        $url = curl_init($this->queryPaymentURL . $paymentID);

        $header = array(
            'Content-Type:application/json',
            'authorization:' . $token,
            'x-app-key:' . $this->app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        $data = json_decode($resultdata);
        curl_close($url);

        return $data;
    }
}
