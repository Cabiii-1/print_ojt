<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 10px;
            margin: 0;
            line-height: 1.4;
        }
        h2 {
            font-size: 24px;
            color: #1a202c;
            margin-bottom: 15px;
        }
        .container {
            max-width: 98%;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        /* Header styling */
        thead {
            background-color: #f8f9fa;
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #2d3748;
            border: 1px solid #718096;
            padding: 12px;
            text-align: left;
            background-color: #edf2f7;
        }
        /* Cell styling */
        td {
            padding: 12px;
            border: 1px solid #718096;
            color: #4a5568;
            font-size: 14px;
        }
        /* Zebra striping */
        tr:nth-child(even) td {
            background-color: #f7fafc;
        }
        /* Number alignment */
        .text-right {
            text-align: right;
        }
        /* Border styling */
        table {
            border: 2px solid #4a5568;
        }
        th, td {
            position: relative;
        }
        th {
            border-bottom: 2px solid #4a5568;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Print Preview</h2>
        <table>
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td @if(is_numeric($cell)) class="text-right" @endif>
                                {{ $cell }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
