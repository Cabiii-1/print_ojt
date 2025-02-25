<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UserController extends Controller
{
    public function index()
    {
        // Google Apps Script Web App URL
        $url = 'https://script.google.com/macros/s/AKfycbwkl1mdCpm-moQVadvcbsqFQupCDC7uWSGATz-0B9drXh9Q3dpWGOy90Mz2xllqc1Uw/exec';

        // Initialize Guzzle HTTP Client
        $client = new Client();

        try {
            // Make a GET request to the API
            $response = $client->get($url);
            $apiResponse = json_decode($response->getBody(), true);

            // Extract headers (column names) from the first row of data
            $headers = [];
            if (!empty($apiResponse['data'])) {
                $headers = array_keys($apiResponse['data'][0]);
            }

            // Pass the data and headers to the view
            return view('sheet-data', [
                'data' => $apiResponse['data'],
                'headers' => $headers,
            ]);
        } catch (\Exception $e) {
            // Handle errors
            return view('sheet-data', ['error' => 'Failed to fetch data from Google Sheets.']);
        }
    }
}