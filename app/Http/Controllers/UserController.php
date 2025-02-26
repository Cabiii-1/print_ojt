<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UserController extends Controller
{
    private $scriptUrl = 'https://script.google.com/macros/s/AKfycbwkl1mdCpm-moQVadvcbsqFQupCDC7uWSGATz-0B9drXh9Q3dpWGOy90Mz2xllqc1Uw/exec';

    public function index()
    {
        // Initialize Guzzle HTTP Client
        $client = new Client();

        try {
            // Make a GET request to the API
            $response = $client->get($this->scriptUrl);
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

    public function getSheets()
    {
        $client = new Client();
        try {
            $response = $client->get($this->scriptUrl . '?action=getSheets');
            $rawResponse = $response->getBody()->getContents();
            \Log::info('Raw response:', ['response' => $rawResponse]);
            
            $data = json_decode($rawResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            if (!isset($data['sheets']) && isset($data[0])) {
                // If sheets is not an object property but the response is an array
                return response()->json(['sheets' => $data]);
            }
            
            // Log the decoded data
            \Log::info('Decoded data:', ['data' => $data]);
            
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch sheets:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch sheets: ' . $e->getMessage()], 500);
        }
    }

    public function getSheetData(Request $request)
    {
        $sheet = $request->input('sheet');
        if (!$sheet) {
            return response()->json(['error' => 'Sheet name is required.'], 400);
        }

        $client = new Client();
        try {
            $response = $client->get($this->scriptUrl . '?sheet=' . urlencode($sheet));
            return response()->json(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch sheet data.'], 500);
        }
    }
}