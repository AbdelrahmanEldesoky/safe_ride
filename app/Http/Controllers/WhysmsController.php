<?php

namespace App\Http\Controllers;

use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use User;

class WhysmsController extends Controller
{
    public function sendBulkSMS(Request $request)
    {
        // Set the API endpoint
        $endpoint = 'https://bulk.whysms.com/api/v3/sms/send';

        // Set the API token
        $apiToken = '249|Un2xZKpfAo4CklkRgAl24FOVxBqAJsTRrnQa8w6A';

        // Set the request parameters
        $params = [
            'recipient' => '+201200816003',
            'sender_id' => 'WhySMS Test',
            'type' => 'plain',
            'message' => 'This is a test message',
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
