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
            animation: fadeIn 0.8s ease-out;
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
            animation: fadeIn 0.5s ease-out;
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

        .stats-card {
            text-align: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8fafc;
            transition: var(--transition);
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

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
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

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr {
            transition: var(--transition);
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
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

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive design */
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
                    <div class="stat-number" id="total-records">27,000+</div>
                    <div class="stat-label">Total Records</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number" id="total-countries">200+</div>
                    <div class="stat-label">Countries</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number" id="disaster-types">50+</div>
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
                        <option value="Flood">Flood</option>
                        <option value="Earthquake">Earthquake</option>
                        <option value="Storm">Storm</option>
                        <option value="Drought">Drought</option>
                        <option value="Volcanic activity">Volcanic Activity</option>
                        <option value="Epidemic">Epidemic</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Country</label>
                    <select class="form-select" id="country">
                        <option value="">All Countries</option>
                        <option value="USA">United States</option>
                        <option value="JAM">Jamaica</option>
                        <option value="JPN">Japan</option>
                        <option value="TUR">Türkiye</option>
                        <option value="IND">India</option>
                        <option value="GTM">Guatemala</option>
                        <!-- More countries will be loaded dynamically -->
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
                    <label class="form-label">Sort By</label>
                    <select class="form-select" id="sort-by">
                        <option value="start_year">Year (Ascending)</option>
                        <option value="start_year_desc">Year (Descending)</option>
                        <option value="total_deaths">Deaths (Ascending)</option>
                        <option value="total_deaths_desc">Deaths (Descending)</option>
                        <option value="magnitude">Magnitude (Ascending)</option>
                        <option value="magnitude_desc">Magnitude (Descending)</option>
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
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
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
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Sample data for demonstration - in a real application, this would come from your backend
        const sampleData = [
            {
                dis_no: "1900-0003-USA",
                historic: "Yes",
                classification_key: "nat-met-sto-tro",
                disaster_group: "Natural",
                disaster_subgroup: "Meteorological",
                disaster_type: "Storm",
                disaster_subtype: "Tropical cyclone",
                event_name: "",
                iso: "USA",
                country: "United States of America",
                subregion: "Northern America",
                region: "Americas",
                location: "Galveston (Texas)",
                origin: "",
                magnitude: 220,
                magnitude_scale: "Kph",
                start_year: 1900,
                start_month: 9,
                start_day: 8,
                end_year: 1900,
                end_month: 9,
                end_day: 8,
                total_deaths: 6000,
                no_injured: null,
                no_affected: null,
                no_homeless: null,
                total_affected: null,
                total_damage_adjusted: 1131126,
                entry_date: "2004-10-18",
                last_update: "2023-10-17"
            },
            {
                dis_no: "1900-0005-USA",
                historic: "Yes",
                classification_key: "tec-ind-fir-fir",
                disaster_group: "Technological",
                disaster_subgroup: "Industrial accident",
                disaster_type: "Fire (Industrial)",
                disaster_subtype: "Fire (Industrial)",
                event_name: "",
                iso: "USA",
                country: "United States of America",
                subregion: "Northern America",
                region: "Americas",
                location: "Hoboken, New York, Piers",
                origin: "",
                magnitude: null,
                magnitude_scale: "m3",
                start_year: 1900,
                start_month: 6,
                start_day: 30,
                end_year: 1900,
                end_month: 6,
                end_day: 30,
                total_deaths: 300,
                no_injured: null,
                no_affected: null,
                no_homeless: null,
                total_affected: null,
                total_damage_adjusted: null,
                entry_date: "2003-07-01",
                last_update: "2023-09-25"
            },
            // More sample data would be added here
        ];

        // Initialize DataTable
        let dataTable;
        
        $(document).ready(function() {
            // Initialize with sample data
            initializeTable(sampleData);
            
            // Apply filters button
            $('#apply-filters').click(function() {
                applyFilters();
            });
            
            // Reset filters button
            $('#reset-filters').click(function() {
                resetFilters();
            });
            
            // Export CSV button
            $('#export-csv').click(function() {
                exportToCSV();
            });
        });

        function initializeTable(data) {
            if ($.fn.DataTable.isDataTable('#disaster-table')) {
                dataTable.destroy();
            }
            
            dataTable = $('#disaster-table').DataTable({
                data: data,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                columns: [
                    { data: 'start_year' },
                    { data: 'disaster_type' },
                    { data: 'country' },
                    { 
                        data: 'event_name',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        }
                    },
                    { 
                        data: 'location',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        }
                    },
                    { 
                        data: 'magnitude',
                        render: function(data, type, row) {
                            return data ? `${data} ${row.magnitude_scale || ''}` : 'N/A';
                        }
                    },
                    { 
                        data: 'total_deaths',
                        render: function(data, type, row) {
                            return data ? data.toLocaleString() : 'N/A';
                        }
                    },
                    { 
                        data: 'total_affected',
                        render: function(data, type, row) {
                            return data ? data.toLocaleString() : 'N/A';
                        }
                    },
                    { 
                        data: 'total_damage_adjusted',
                        render: function(data, type, row) {
                            return data ? `$${data.toLocaleString()}` : 'N/A';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-outline-primary view-details" data-id="${row.dis_no}">Details</button>`;
                        }
                    }
                ],
                order: [[0, 'desc']],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            
            // Update results count
            updateResultsCount();
            
            // Add event listener for details buttons
            $('#disaster-table').on('click', '.view-details', function() {
                const id = $(this).data('id');
                showDetails(id);
            });
        }

        function applyFilters() {
            // Show loading indicator
            $('#loading').show();
            
            // In a real application, you would make an AJAX call to your backend
            // For this example, we'll simulate a delay and then filter the sample data
            setTimeout(() => {
                const disasterType = $('#disaster-type').val();
                const country = $('#country').val();
                const yearFrom = $('#year-from').val();
                const yearTo = $('#year-to').val();
                const magnitudeFrom = $('#magnitude-from').val();
                const magnitudeTo = $('#magnitude-to').val();
                const searchText = $('#search-text').val().toLowerCase();
                const deathsFrom = $('#deaths-from').val();
                const deathsTo = $('#deaths-to').val();
                const sortBy = $('#sort-by').val();
                
                // Update active filters display
                updateActiveFilters(disasterType, country, yearFrom, yearTo, 
                                  magnitudeFrom, magnitudeTo, deathsFrom, deathsTo, searchText);
                
                // Filter the data (in a real app, this would be done on the server)
                let filteredData = sampleData.filter(item => {
                    // Disaster type filter
                    if (disasterType && item.disaster_type !== disasterType) return false;
                    
                    // Country filter
                    if (country && item.iso !== country) return false;
                    
                    // Year range filter
                    if (yearFrom && item.start_year < parseInt(yearFrom)) return false;
                    if (yearTo && item.start_year > parseInt(yearTo)) return false;
                    
                    // Magnitude range filter
                    if (magnitudeFrom && (!item.magnitude || item.magnitude < parseFloat(magnitudeFrom))) return false;
                    if (magnitudeTo && (!item.magnitude || item.magnitude > parseFloat(magnitudeTo))) return false;
                    
                    // Deaths range filter
                    if (deathsFrom && (!item.total_deaths || item.total_deaths < parseInt(deathsFrom))) return false;
                    if (deathsTo && (!item.total_deaths || item.total_deaths > parseInt(deathsTo))) return false;
                    
                    // Search text filter
                    if (searchText) {
                        const searchableText = [
                            item.event_name || '',
                            item.location || '',
                            item.disaster_type || '',
                            item.country || ''
                        ].join(' ').toLowerCase();
                        
                        if (!searchableText.includes(searchText)) return false;
                    }
                    
                    return true;
                });
                
                // Sort the data
                if (sortBy === 'start_year') {
                    filteredData.sort((a, b) => a.start_year - b.start_year);
                } else if (sortBy === 'start_year_desc') {
                    filteredData.sort((a, b) => b.start_year - a.start_year);
                } else if (sortBy === 'total_deaths') {
                    filteredData.sort((a, b) => (a.total_deaths || 0) - (b.total_deaths || 0));
                } else if (sortBy === 'total_deaths_desc') {
                    filteredData.sort((a, b) => (b.total_deaths || 0) - (a.total_deaths || 0));
                } else if (sortBy === 'magnitude') {
                    filteredData.sort((a, b) => (a.magnitude || 0) - (b.magnitude || 0));
                } else if (sortBy === 'magnitude_desc') {
                    filteredData.sort((a, b) => (b.magnitude || 0) - (a.magnitude || 0));
                }
                
                // Update the table
                initializeTable(filteredData);
                
                // Hide loading indicator
                $('#loading').hide();
            }, 1000);
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
            $('#sort-by').val('start_year');
            
            // Clear active filters
            $('#active-filters').empty();
            
            // Reset table to show all data
            initializeTable(sampleData);
        }

        function updateActiveFilters(disasterType, country, yearFrom, yearTo, 
                                   magnitudeFrom, magnitudeTo, deathsFrom, deathsTo, searchText) {
            $('#active-filters').empty();
            
            if (disasterType) {
                $('#active-filters').append(
                    `<div class="filter-tag">
                        Type: ${disasterType}
                        <i class="fas fa-times" onclick="$('#disaster-type').val(''); applyFilters();"></i>
                    </div>`
                );
            }
            
            if (country) {
                $('#active-filters').append(
                    `<div class="filter-tag">
                        Country: ${$('#country option:selected').text()}
                        <i class="fas fa-times" onclick="$('#country').val(''); applyFilters();"></i>
                    </div>`
                );
            }
            
            if (yearFrom || yearTo) {
                const yearText = `${yearFrom || '1900'} to ${yearTo || '2023'}`;
                $('#active-filters').append(
                    `<div class="filter-tag">
                        Years: ${yearText}
                        <i class="fas fa-times" onclick="$('#year-from').val(''); $('#year-to').val(''); applyFilters();"></i>
                    </div>`
                );
            }
            
            if (magnitudeFrom || magnitudeTo) {
                const magnitudeText = `${magnitudeFrom || 'Min'} to ${magnitudeTo || 'Max'}`;
                $('#active-filters').append(
                    `<div class="filter-tag">
                        Magnitude: ${magnitudeText}
                        <i class="fas fa-times" onclick="$('#magnitude-from').val(''); $('#magnitude-to').val(''); applyFilters();"></i>
                    </div>`
                );
            }
            
            if (deathsFrom || deathsTo) {
                const deathsText = `${deathsFrom || 'Min'} to ${deathsTo || 'Max'}`;
                $('#active-filters').append(
                    `<div class="filter-tag">
                        Deaths: ${deathsText}
                        <i class="fas fa-times" onclick="$('#deaths-from').val(''); $('#deaths-to').val(''); applyFilters();"></i>
                    </div>`
                );
            }
            
            if (searchText) {
                $('#active-filters').append(
                    `<div class="filter-tag">
                        Search: "${searchText}"
                        <i class="fas fa-times" onclick="$('#search-text').val(''); applyFilters();"></i>
                    </div>`
                );
            }
        }

        function updateResultsCount() {
            const info = dataTable.page.info();
            $('#results-count').text(`Showing ${info.recordsDisplay} of ${info.recordsTotal} records`);
        }

        function showDetails(id) {
            // Find the record with this ID
            const record = sampleData.find(item => item.dis_no === id);
            
            if (record) {
                // Format the details for display
                const detailsHtml = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <p><strong>Disaster ID:</strong> ${record.dis_no}</p>
                            <p><strong>Type:</strong> ${record.disaster_type} - ${record.disaster_subtype}</p>
                            <p><strong>Group:</strong> ${record.disaster_group} / ${record.disaster_subgroup}</p>
                            <p><strong>Country:</strong> ${record.country} (${record.iso})</p>
                            <p><strong>Region:</strong> ${record.region} / ${record.subregion}</p>
                            <p><strong>Location:</strong> ${record.location || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Timing & Impact</h5>
                            <p><strong>Date:</strong> ${record.start_year}-${record.start_month}-${record.start_day} to ${record.end_year}-${record.end_month}-${record.end_day}</p>
                            <p><strong>Magnitude:</strong> ${record.magnitude || 'N/A'} ${record.magnitude_scale || ''}</p>
                            <p><strong>Deaths:</strong> ${record.total_deaths ? record.total_deaths.toLocaleString() : 'N/A'}</p>
                            <p><strong>Affected:</strong> ${record.total_affected ? record.total_affected.toLocaleString() : 'N/A'}</p>
                            <p><strong>Damage (Adjusted):</strong> ${record.total_damage_adjusted ? `$${record.total_damage_adjusted.toLocaleString()}` : 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Additional Information</h5>
                            <p><strong>Origin:</strong> ${record.origin || 'N/A'}</p>
                            <p><strong>Event Name:</strong> ${record.event_name || 'N/A'}</p>
                            <p><strong>Historic:</strong> ${record.historic}</p>
                            <p><strong>Classification Key:</strong> ${record.classification_key}</p>
                            <p><strong>Last Updated:</strong> ${record.last_update}</p>
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
            // In a real application, this would generate a CSV from the filtered data
            alert("In a real application, this would export the filtered data to a CSV file.");
        }
    </script>
</body>
</html>