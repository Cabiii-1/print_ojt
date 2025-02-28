<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        // Get data from the URL parameters
        $id = $request->query('id');
        $course = $request->query('course');
        $studentName = $request->query('student_name');

        // Fetch more details from the database if needed
        // Example: $student = Student::find($id);

        // Example data to pass to the PDF
        $data = [
            'id' => $id,
            'course' => $course,
            'student_name' => $studentName,
            'date' => now()->format('Y-m-d'),
        ];

        // Load the PDF view and pass data
        $pdf = Pdf::loadView('pdf.template', $data);

        // Return the PDF for preview in a browser
        return $pdf->stream('Student_Report.pdf');
    }
}