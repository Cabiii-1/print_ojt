<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        @if (isset($error))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p class="font-medium">{{ $error }}</p>
            </div>
        @else
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1 flex items-center gap-4">
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="column-menu" aria-expanded="true" aria-haspopup="true">
                            <span>Columns</span>
                            <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="column-menu">
                                @foreach ($headers as $header)
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                        <input type="checkbox" id="checkbox-{{ $header }}" class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked
                                               onchange="toggleColumn('{{ $header }}')">
                                        <span class="ml-3">{{ $header }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <input type="text"
                           id="searchInput"
                           placeholder="Search in table..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <button onclick="handlePrint()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Preview
                </button>
            </div>

            <div class="overflow-hidden rounded-lg shadow-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <th scope="col" class="sticky top-0 px-6 py-4 border-b border-gray-200 bg-opacity-75 backdrop-blur backdrop-filter">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="select-all" class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </label>
                            </th>
                            @foreach ($headers as $header)
                                <th scope="col" id="header-{{ $header }}" class="sticky top-0 px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 bg-opacity-75 backdrop-blur backdrop-filter">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($data as $index => $row)
                            <tr class="even:bg-gray-50 hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap border-r border-gray-100">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="row-selector form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" data-index="{{ $index }}">
                                    </label>
                                </td>
                                @foreach ($headers as $header)
                                    <td class="column-{{ $header }} px-6 py-4 whitespace-nowrap text-sm text-gray-600 border-r border-gray-100 @if(is_numeric($row[$header] ?? '')) text-right @endif">
                                        {{ $row[$header] ?? '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <style>
        .hidden-column {
            display: none;
        }
        /* Custom scrollbar styling */
        .overflow-hidden::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .overflow-hidden::-webkit-scrollbar-track {
            background: #f7fafc;
        }
        .overflow-hidden::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
        .overflow-hidden::-webkit-scrollbar-thumb:hover {
            background: #cbd5e0;
        }
    </style>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td:not(:first-child):not(.hidden-column)');
                let found = false;
                
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchText)) {
                        found = true;
                    }
                });
                
                row.style.display = found ? '' : 'none';
            });
        });

        // Toggle column visibility
        function toggleColumn(column) {
            const checkbox = document.getElementById(`checkbox-${column}`);
            const cells = document.querySelectorAll(`.column-${column}`);
            const header = document.getElementById(`header-${column}`);

            cells.forEach(cell => cell.classList.toggle('hidden-column', !checkbox.checked));
            header.classList.toggle('hidden-column', !checkbox.checked);
        }

        // Select all rows
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-selector');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function handlePrint() {
            const selectedRows = Array.from(document.querySelectorAll('.row-selector:checked'))
                .map(checkbox => checkbox.closest('tr'));

            if (selectedRows.length === 0) {
                alert("No rows selected for printing.");
                return;
            }

            // Get visible headers (not hidden)
            const visibleHeaders = Array.from(document.querySelectorAll('th[id^="header-"]:not(.hidden-column)'))
                .map(th => th.textContent.trim());

            // Get data from selected rows, but only for visible columns
            const selectedData = selectedRows.map(row => {
                const cells = Array.from(row.querySelectorAll('td:not(.hidden-column):not(:first-child)'))
                    .map(td => td.textContent.trim());
                return cells;
            });

            fetch("{{ route('print.preview') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ 
                    selectedData,
                    headers: visibleHeaders
                })
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                window.open(url);
            })
            .catch(error => console.error("Error printing:", error));
        }
    </script>
</body>
</html>