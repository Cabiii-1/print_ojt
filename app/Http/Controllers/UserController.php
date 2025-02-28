<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UserController extends Controller
{
    private $scriptUrl = 'https://script.google.com/macros/s/AKfycbwkl1mdCpm-moQVadvcbsqFQupCDC7uWSGATz-0B9drXh9Q3dpWGOy90Mz2xllqc1Uw/exec';

    public function index($sheet = null)
    {
        // Initialize Guzzle HTTP Client
        $client = new Client();

        try {
            // Make a GET request to the API with optional sheet parameter
            $url = $this->scriptUrl;
            if ($sheet) {
                $url .= '?sheet=' . urlencode($sheet);
            }
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
            return response()->json(['error' => true, 'message' => 'Sheet name is required.'], 400);
        }

        $client = new Client();
        try {
            // Build query parameters
            $params = [
                'sheet' => $sheet,
                'page' => $request->input('page', 1),
                'per_page' => $request->input('per_page', 100)
            ];

            // Build URL with query parameters
            $url = $this->scriptUrl . '?' . http_build_query($params);
            \Log::info('Requesting sheet data:', ['url' => $url]);

            $response = $client->get($url);
            $rawResponse = $response->getBody()->getContents();
            \Log::info('Sheet data raw response:', [
                'sheet' => $sheet,
                'response' => $rawResponse
            ]);
            
            $data = json_decode($rawResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            // Check for Apps Script error response
            if (isset($data['error']) && $data['error'] === true) {
                throw new \Exception($data['message'] ?? 'Unknown error from Apps Script');
            }

            // If the response is just an array, wrap it in data property
            if (is_array($data) && !isset($data['data'])) {
                $data = ['data' => $data];
            }
            
            \Log::info('Sheet data processed:', [
                'sheet' => $sheet,
                'rowCount' => count($data['data'] ?? [])
            ]);
            
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch sheet data:', [
                'sheet' => $sheet,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch sheet data: ' . $e->getMessage()
            ], 500);
        }
    }
}