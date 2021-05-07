<?php

namespace App\Payment;

use Illuminate\Support\Facades\Log;

class Payment
{
    // CURRENCIES
    // AZN => 944
    // USD => 840
    // EUR => 978

    public static function redirectToPayment($amount = "1", $description = "Test Purchase", $language = "az", $currency = "944")
    {
        $ca = base_path('payment/PSroot.pem');
        $key = base_path('payment/rsa_key_pair.pem');
        $cert = base_path('payment/keystore.pkcs12');
        $password = "P@ssword";

        $merchant_handler = "https://ecomm.pashabank.az:18443/ecomm2/MerchantHandler";
        $client_handler = "https://ecomm.pashabank.az:8463/ecomm2/ClientHandler";

        $errors = [];

        if (!is_numeric($amount) || strlen($amount) < 1 || strlen($amount) > 12) array_push($errors, 'Incorrect amount!');
        if (!is_numeric($currency) || strlen($currency) != 3) array_push($errors, 'Incorrect currency!');
        if (strlen($description) > 125) array_push($errors, 'Incorrect description!');
        if (!ctype_alpha($language)) array_push($errors, 'Incorrect language!');

        $params['command'] = "V";
        $params['amount'] = $amount;
        $params['currency'] = $currency;
        $params['description'] = $description;
        $params['language'] = $language;
        $params['msg_type'] = "SMS";

        if (filter_input(INPUT_SERVER, 'REMOTE_ADDR') != null) {
            $params['client_ip_addr'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR') != null) {
            $params['client_ip_addr'] = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP') != null) {
            $params['client_ip_addr'] = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } else {
            $params['client_ip_addr'] = "127.0.0.1";
        }

        $qstring = http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $merchant_handler);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qstring);
        curl_setopt($ch, CURLOPT_SSLCERT, $cert);
        curl_setopt($ch, CURLOPT_SSLKEY, $key);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, "PEM");
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $password);
        curl_setopt($ch, CURLOPT_CAPATH, $ca);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, "P12");
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $result = curl_exec($ch);

        // Example for returned result
        // TRANSACTION_ID: TwXcbhBgrIsMY0A7s982nx/pSzE=
        if (curl_error($ch)) array_push($errors, 'Payment error!');

        curl_close($ch);

        $trans_ref = explode(' ', $result)[1];
        $trans_ref = urlencode($trans_ref);
        $client_url = $client_handler . "?trans_id=" . $trans_ref;

        return ['errors' => $errors, 'trans_id' => $trans_ref, 'client_url' => $client_url];


        //
    }

    public static function getPaymentDetails($trans_id)
    {
        if (!isset($trans_id) || empty($trans_id) && !is_string($trans_id)) abort(403, 'Incorrect transaction id');

        $ca = base_path('payment/PSroot.pem');
        $key = base_path('payment/rsa_key_pair.pem');
        $cert = base_path('payment/keystore.pkcs12');
        $password = "P@ssword";

        $merchant_handler = "https://ecomm.pashabank.az:18443/ecomm2/MerchantHandler";
        $client_handler = "https://ecomm.pashabank.az:8463/ecomm2/ClientHandler";

        $errors = [];
        $paymentDetails = [];

        $success_page = "success.html";
        $card_expired_page = "card_expired.html";
        $insufficient_funds_page = "insufficient_funds.html";
        $system_malfunction_page = "system_malfunction.html";
        // Example for Query String response to RETURN_OK_URL:
        // ?trans_id=5h78PCxRzsRSzLxuDEWDyhSeC44=&amp;Ucaf_Cardholder_Confirm=0

        if (
            // strlen($trans_id) != 20 ||
            base64_encode(base64_decode($trans_id)) != $trans_id
        ) {
            abort(403, 'Incorrect transaction id');
        }

        $params['command'] = "C";
        $params['trans_id'] = $trans_id;

        if (filter_input(INPUT_SERVER, 'REMOTE_ADDR') != null) {
            $params['client_ip_addr'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR') != null) {
            $params['client_ip_addr'] =
                filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP') != null) {
            $params['client_ip_addr'] = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } else {
            $params['client_ip_addr'] = "10.10.10.10";
        }

        $qstring = http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $merchant_handler);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qstring);
        curl_setopt($ch, CURLOPT_SSLCERT, $cert);
        curl_setopt($ch, CURLOPT_SSLKEY, $key);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, "PEM");
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $password);
        curl_setopt($ch, CURLOPT_CAPATH, $ca);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, "P12");
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $result = curl_exec($ch);

        // Example returning result:
        // RESULT: OK
        // RESULT_PS: FINISHED
        // RESULT_CODE: 000
        // 3DSECURE: ATTEMPTED
        // RRN: 123456789012
        // APPROVAL_CODE: 123456
        // CARD_NUMBER: 4***********9999
        // RECC_PMNT_ID: 1258
        // RECC_PMNT_EXPIRY: 1108
        // for debug reasons only!

        if (curl_error($ch)) array_push($errors, 'Payment error!');

        curl_close($ch);

        $r_hm = array();
        $r_arr = array();

        $r_arr = explode("\n", $result);

        for ($i = 0; $i < count($r_arr); $i++) {
            $param = explode(":", $r_arr[$i])[0];
            $value = substr(explode(":", $r_arr[$i])[1], 1);
            $r_hm[$param] = $value;
        }

        if ($r_hm["RESULT"] == "OK") {
            if ($r_hm["RESULT_CODE"] == "000") $paymentDetails['status'] = 'completed';
            else $paymentDetails['status'] = 'not_completed';
        } elseif ($r_hm["RESULT"] == "FAILED") {
            if ($r_hm["RESULT_CODE"] == "116") $paymentDetails['status'] = 'insufficent_funds';
            elseif ($r_hm["RESULT_CODE"] == "129") $paymentDetails['status'] = 'card_expired';
            elseif ($r_hm["RESULT_CODE"] == "909") $paymentDetails['status'] = 'system_malfunction';
            else $paymentDetails['status'] = 'system_malfunction';
        } elseif ($r_hm["RESULT"] == "TIMEOUT") $paymentDetails['status'] = 'timeout';
        else $paymentDetails['status'] = 'system_malfunction';

        if ($r_hm["RESULT"] == "FAILED") {
            Log::info([
                'transaction_id' => $trans_id,
                'error_code' => $r_hm["RESULT_CODE"],
                'rrn' => $r_hm["RRN"]
            ]);
        }


        return ['trans_id' => $trans_id, 'errors' => $errors, 'paymentDetails' => $paymentDetails];
    }
}
