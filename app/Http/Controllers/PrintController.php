<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function printPreview(Request $request)
    {
        // Retrieve selected rows and headers from the request
        $selectedData = $request->input('selectedData', []);
        $headers = $request->input('headers', []);

        if (empty($selectedData)) {
            return back()->with('error', 'No data selected for printing.');
        }

        // Pass both headers and data to the view
        $pdf = Pdf::loadView('print.preview', [
            'headers' => $headers,
            'data' => $selectedData
        ]);
        
        return $pdf->stream('Print_Preview.pdf');
    }
}
