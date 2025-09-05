<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster History Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --light: #f8fafc;
            --dark: #212529;
            --success: #16a34a;
            --warning: #eab308;
            --danger: #dc2626;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            color: #334155;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }

        .stats-card {
            text-align: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8fafc;
            transition: var(--transition);
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-label {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .filter-tag {
            background: #e0e7ff;
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .filter-tag i {
            cursor: pointer;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background-color: #f1f5f9;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 1200px) {
            .container {
                padding: 0 15px;
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-history"></i>
            </div>
            <h1>Disaster History Explorer</h1>
            <p class="subtitle">Explore and analyze historical disaster data from around the world</p>
        </div>

        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number" id="total-records">0</div>
                    <div class="stat-label">Total Records</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number" id="total-countries">0</div>
                    <div class="stat-label">Countries</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number" id="disaster-types">0</div>
                    <div class="stat-label">Disaster Types</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number">1900-2023</div>
                    <div class="stat-label">Time Period</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card">
            <h3 class="mb-4"><i class="fas fa-filter"></i> Filter Data</h3>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Disaster Type</label>
                    <select class="form-select" id="disaster-type">
                        <option value="">All Types</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Country</label>
                    <select class="form-select" id="country">
                        <option value="">All Countries</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Year Range</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="year-from" placeholder="From" min="1900" max="2023">
                        <span class="input-group-text">to</span>
                        <input type="number" class="form-control" id="year-to" placeholder="To" min="1900" max="2023">
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Magnitude Range</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="magnitude-from" placeholder="Min" step="0.1">
                        <span class="input-group-text">to</span>
                        <input type="number" class="form-control" id="magnitude-to" placeholder="Max" step="0.1">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="search-text" placeholder="Search in event names, locations...">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Deaths Range</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="deaths-from" placeholder="Min">
                        <span class="input-group-text">to</span>
                        <input type="number" class="form-control" id="deaths-to" placeholder="Max">
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Results Per Page</label>
                    <select class="form-select" id="per-page">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button id="apply-filters" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <button id="reset-filters" class="btn btn-outline-primary">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button id="export-csv" class="btn btn-outline-primary ms-auto">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
            
            <div class="filter-tags" id="active-filters">
                <!-- Active filters will appear here -->
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="loading">
            <div class="loading-spinner"></div>
            <p>Loading disaster data...</p>
        </div>

        <!-- Results Section -->
        <div class="card">
            <h3 class="mb-4"><i class="fas fa-table"></i> Disaster Records</h3>
            
            <div class="table-responsive">
                <table class="data-table" id="disaster-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Disaster Type</th>
                            <th>Country</th>
                            <th>Event Name</th>
                            <th>Location</th>
                            <th>Magnitude</th>
                            <th>Deaths</th>
                            <th>Affected</th>
                            <th>Damage (Adjusted)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here -->
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <span id="results-count">Showing 0 of 0 records</span>
                </div>
                <nav>
                    <ul class="pagination" id="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#" data-page="prev">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#" data-page="1">1</a></li>
                        <li class="page-item"><a class="page-link" href="#" data-page="next">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal for detailed view -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disaster Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <!-- Details will be populated here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Global variables
        let currentPage = 1;
        let perPage = 25;
        let totalRecords = 0;
        let allData = [];
        let uniqueCountries = new Set();
        let uniqueDisasterTypes = new Set();

        $(document).ready(function() {
            // Load initial data
            loadData();
            
            // Apply filters button
            $('#apply-filters').click(function() {
                currentPage = 1;
                loadData();
            });
            
            // Reset filters button
            $('#reset-filters').click(function() {
                resetFilters();
            });
            
            // Export CSV button
            $('#export-csv').click(function() {
                exportToCSV();
            });
            
            // Per page change
            $('#per-page').change(function() {
                perPage = parseInt($(this).val());
                currentPage = 1;
                loadData();
            });
            
            // Pagination click handler
            $('#pagination').on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                
                if (page === 'prev' && currentPage > 1) {
                    currentPage--;
                } else if (page === 'next' && currentPage < Math.ceil(totalRecords / perPage)) {
                    currentPage++;
                } else if (typeof page === 'number') {
                    currentPage = page;
                }
                
                loadData();
            });
        });

        function loadData() {
            // Show loading indicator
            $('#loading').show();
            
            // Collect filters
            const filters = {
                disaster_type: $('#disaster-type').val(),
                country: $('#country').val(),
                year_from: $('#year-from').val(),
                year_to: $('#year-to').val(),
                magnitude_from: $('#magnitude-from').val(),
                magnitude_to: $('#magnitude-to').val(),
                deaths_from: $('#deaths-from').val(),
                deaths_to: $('#deaths-to').val()
            };
            
            const search = $('#search-text').val();
            
            // Update active filters display
            updateActiveFilters(filters, search);
            
            // Make AJAX request to backend
            $.ajax({
                url: 'data2.php',
                type: 'POST',
                data: {
                    filters: filters,
                    search: search,
                    page: currentPage,
                    perPage: perPage
                },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (data.error) {
                            alert('Error: ' + data.error);
                            return;
                        }
                        
                        // Update stats
                        $('#total-records').text(data.total.toLocaleString());
                        $('#total-countries').text(uniqueCountries.size);
                        $('#disaster-types').text(uniqueDisasterTypes.size);
                        
                        // Update results count
                        $('#results-count').text(`Showing ${data.data.length} of ${data.total} records`);
                        
                        // Update pagination
                        updatePagination(data.total);
                        
                        // Render table
                        renderTable(data.data);
                        
                        // Update dropdowns with unique values
                        updateFilterDropdowns(data.data);
                        
                    } catch (e) {
                        console.error('Error parsing response:', e, response);
                        alert('Error loading data. Please check the console for details.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    alert('Error loading data. Please check the console for details.');
                },
                complete: function() {
                    // Hide loading indicator
                    $('#loading').hide();
                }
            });
        }

        function renderTable(data) {
            const tbody = $('#disaster-table tbody');
            tbody.empty();
            
            if (data.length === 0) {
                tbody.append('<tr><td colspan="10" class="text-center">No records found</td></tr>');
                return;
            }
            
            data.forEach(item => {
                // Track unique values for stats
                if (item.Country) uniqueCountries.add(item.Country);
                if (item['Disaster Type']) uniqueDisasterTypes.add(item['Disaster Type']);
                
                const row = `
                    <tr>
                        <td>${item['Start Year'] || 'N/A'}</td>
                        <td>${item['Disaster Type'] || 'N/A'}</td>
                        <td>${item.Country || 'N/A'}</td>
                        <td>${item['Event Name'] || 'N/A'}</td>
                        <td>${item.Location || 'N/A'}</td>
                        <td>${item.Magnitude ? `${item.Magnitude} ${item['Magnitude Scale'] || ''}` : 'N/A'}</td>
                        <td>${item['Total Deaths'] ? parseInt(item['Total Deaths']).toLocaleString() : 'N/A'}</td>
                        <td>${item['Total Affected'] ? parseInt(item['Total Affected']).toLocaleString() : 'N/A'}</td>
                        <td>${item['Total Damage Adjusted (\'000 US$)'] ? `$${parseInt(item['Total Damage Adjusted (\'000 US$)']).toLocaleString()}` : 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary view-details" data-id="${item['DisNo.']}">
                                Details
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
            
            // Add event listener for details buttons
            $('.view-details').click(function() {
                const id = $(this).data('id');
                showDetails(id, data);
            });
        }

        function updatePagination(total) {
            totalRecords = total;
            const totalPages = Math.ceil(total / perPage);
            const pagination = $('#pagination');
            pagination.empty();
            
            // Previous button
            pagination.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="prev">Previous</a>
                </li>
            `);
            
            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                pagination.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }
            
            // Next button
            pagination.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="next">Next</a>
                </li>
            `);
        }

        function updateFilterDropdowns(data) {
            // Update disaster type dropdown
            const disasterTypes = new Set();
            data.forEach(item => {
                if (item['Disaster Type']) {
                    disasterTypes.add(item['Disaster Type']);
                }
            });
            
            const disasterTypeSelect = $('#disaster-type');
            const currentValue = disasterTypeSelect.val();
            disasterTypeSelect.empty().append('<option value="">All Types</option>');
            
            Array.from(disasterTypes).sort().forEach(type => {
                disasterTypeSelect.append(`<option value="${type}">${type}</option>`);
            });
            
            if (currentValue) {
                disasterTypeSelect.val(currentValue);
            }
            
            // Update country dropdown
            const countries = new Set();
            data.forEach(item => {
                if (item.Country) {
                    countries.add(item.Country);
                }
            });
            
            const countrySelect = $('#country');
            const currentCountryValue = countrySelect.val();
            countrySelect.empty().append('<option value="">All Countries</option>');
            
            Array.from(countries).sort().forEach(country => {
                countrySelect.append(`<option value="${country}">${country}</option>`);
            });
            
            if (currentCountryValue) {
                countrySelect.val(currentCountryValue);
            }
        }

        function updateActiveFilters(filters, search) {
            $('#active-filters').empty();
            
            Object.entries(filters).forEach(([key, value]) => {
                if (value) {
                    let displayName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    let displayValue = value;
                    
                    $('#active-filters').append(`
                        <div class="filter-tag">
                            ${displayName}: ${displayValue}
                            <i class="fas fa-times" onclick="clearFilter('${key}')"></i>
                        </div>
                    `);
                }
            });
            
            if (search) {
                $('#active-filters').append(`
                    <div class="filter-tag">
                        Search: "${search}"
                        <i class="fas fa-times" onclick="$('#search-text').val(''); loadData();"></i>
                    </div>
                `);
            }
        }

        function clearFilter(filterName) {
            $(`#${filterName}`).val('');
            loadData();
        }

        function resetFilters() {
            $('#disaster-type').val('');
            $('#country').val('');
            $('#year-from').val('');
            $('#year-to').val('');
            $('#magnitude-from').val('');
            $('#magnitude-to').val('');
            $('#search-text').val('');
            $('#deaths-from').val('');
            $('#deaths-to').val('');
            
            // Clear active filters
            $('#active-filters').empty();
            
            // Reset to first page
            currentPage = 1;
            
            // Reload data
            loadData();
        }

        function showDetails(id, data) {
            // Find the record with this ID
            const record = data.find(item => item['DisNo.'] === id);
            
            if (record) {
                // Format the details for display
                const detailsHtml = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <p><strong>Disaster ID:</strong> ${record['DisNo.'] || 'N/A'}</p>
                            <p><strong>Type:</strong> ${record['Disaster Type'] || 'N/A'} - ${record['Disaster Subtype'] || 'N/A'}</p>
                            <p><strong>Group:</strong> ${record['Disaster Group'] || 'N/A'} / ${record['Disaster Subgroup'] || 'N/A'}</p>
                            <p><strong>Country:</strong> ${record.Country || 'N/A'} (${record.ISO || 'N/A'})</p>
                            <p><strong>Region:</strong> ${record.Region || 'N/A'} / ${record.Subregion || 'N/A'}</p>
                            <p><strong>Location:</strong> ${record.Location || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Timing & Impact</h5>
                            <p><strong>Date:</strong> ${record['Start Year'] || 'N/A'}-${record['Start Month'] || 'N/A'}-${record['Start Day'] || 'N/A'} to ${record['End Year'] || 'N/A'}-${record['End Month'] || 'N/A'}-${record['End Day'] || 'N/A'}</p>
                            <p><strong>Magnitude:</strong> ${record.Magnitude || 'N/A'} ${record['Magnitude Scale'] || ''}</p>
                            <p><strong>Deaths:</strong> ${record['Total Deaths'] ? parseInt(record['Total Deaths']).toLocaleString() : 'N/A'}</p>
                            <p><strong>Affected:</strong> ${record['Total Affected'] ? parseInt(record['Total Affected']).toLocaleString() : 'N/A'}</p>
                            <p><strong>Damage (Adjusted):</strong> ${record['Total Damage Adjusted (\'000 US$)'] ? `$${parseInt(record['Total Damage Adjusted (\'000 US$)']).toLocaleString()}` : 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Additional Information</h5>
                            <p><strong>Origin:</strong> ${record.Origin || 'N/A'}</p>
                            <p><strong>Event Name:</strong> ${record['Event Name'] || 'N/A'}</p>
                            <p><strong>Historic:</strong> ${record.Historic || 'N/A'}</p>
                            <p><strong>Classification Key:</strong> ${record['Classification Key'] || 'N/A'}</p>
                            <p><strong>Last Updated:</strong> ${record['Last Update'] || 'N/A'}</p>
                        </div>
                    </div>
                `;
                
                // Display in modal
                $('#modal-body').html(detailsHtml);
                const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                modal.show();
            }
        }

        function exportToCSV() {
            // Collect current filters
            const filters = {
                disaster_type: $('#disaster-type').val(),
                country: $('#country').val(),
                year_from: $('#year-from').val(),
                year_to: $('#year-to').val(),
                magnitude_from: $('#magnitude-from').val(),
                magnitude_to: $('#magnitude-to').val(),
                deaths_from: $('#deaths-from').val(),
                deaths_to: $('#deaths-to').val()
            };
            
            const search = $('#search-text').val();
            
            // Create a form and submit it to trigger download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'export_data.php';
            
            // Add filters as hidden inputs
            Object.entries(filters).forEach(([key, value]) => {
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `filters[${key}]`;
                    input.value = value;
                    form.appendChild(input);
                }
            });
            
            if (search) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'search';
                input.value = search;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
</body>
</html>