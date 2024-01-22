<?php

namespace App\Services;

class SMSService
{
    public function sendSMS($phone, $code)
    {
        // Set the API endpoint
        // dd($phone);
        $endpoint = 'https://bulk.whysms.com/api/v3/sms/send';

        // Set the API token
        $apiToken = '249|Un2xZKpfAo4CklkRgAl24FOVxBqAJsTRrnQa8w6A';

        // Set the request parameters
        $params = [
            'recipient' => "+2".$phone,
            'sender_id' => 'WhySMS Test',
            'type' => 'plain',
            'message' => 'This is otp Code '.$code,
        ];

        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

        // Execute the cURL session
        $response = curl_exec($curl);

        // Check for cURL errors or handle the response as needed
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            // Handle the error
            return $error;
        } else {
            // Process the response
            $decodedResponse = json_decode($response, true);
            // Handle the response
            return $decodedResponse;
        }

        // Close the cURL session
        curl_close($curl);
    }
}
