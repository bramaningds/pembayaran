<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $userid     = '0228779340';
        $key        = 'abfdf1a41239eb3d878acd817b3cbcbe';
        $inttime    = strval(time()-strtotime('1970-01-01 00:00:00'));
        $signature  = base64_encode(hash_hmac('sha256', "{$userid}&{$inttime}", $key, true));

        $parameter  = ["NO_TAGIHAN" => "352622591"];

        $header     = [
            "Userid: {$userid}",
            "Signature: {$signature}",
            "Timestamp: {$inttime}",
            "Accept: application/json"
        ];

        try {

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, "http://localhost/pembayaran/bayar");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameter);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    
            $response = curl_exec($curl);

            $this->output->enable_profiler(TRUE);

            echo $response;

        } catch (Exception $e) {
            
            die($e->getMessage());
        }
    }

}