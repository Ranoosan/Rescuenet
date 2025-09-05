<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Disaster Prediction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --info: #4895ef;
        --warning: #f72585;
        --danger: #e63946;
        --light: #f8f9fa;
        --dark: #212529;
        --sidebar-width: 260px;
        --header-height: 70px;
        --card-radius: 12px;
        --transition: all 0.3s ease;
    }

    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
        color: #495057;
    }

    /* Sidebar */
    #sidebar {
        position: fixed;
        width: var(--sidebar-width);
        height: 100vh;
        background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        transition: var(--transition);
        z-index: 1000;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-menu {
        padding: 1rem 0;
    }

    .sidebar-menu .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem 1.5rem;
        margin: 0.25rem 0.5rem;
        border-radius: 8px;
        transition: var(--transition);
    }

    .sidebar-menu .nav-link:hover,
    .sidebar-menu .nav-link.active {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .sidebar-menu .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    /* Main Content */
    #content {
        margin-left: var(--sidebar-width);
        transition: var(--transition);
    }

    /* Header */
    #header {
        height: var(--header-height);
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 0 1.5rem;
    }

    /* Cards */
    .card {
        border: none;
        border-radius: var(--card-radius);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: var(--transition);
        margin-bottom: 1.5rem;
    }

    .card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        transform: translateY(-5px);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid #edf2f9;
        padding: 1.25rem 1.5rem;
        border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
        font-weight: 600;
    }

    /* Stats */
    .stat-card {
        padding: 1.5rem;
        text-align: center;
    }

    .stat-card i {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--primary);
    }

    .stat-card h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-card p {
        color: #6c757d;
        margin-bottom: 0;
    }

    /* Tables */
    .table-container {
        overflow: hidden;
        border-radius: var(--card-radius);
    }

    .table th {
        font-weight: 600;
        background: #f8f9fa;
        border-top: none;
    }

    .table-hover tbody tr {
        transition: var(--transition);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    /* Badges */
    .badge {
        padding: 0.5em 0.8em;
        border-radius: 4px;
        font-weight: 500;
    }

    /* Buttons */
    .btn {
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    /* Charts */
    .chart-container {
        display: flex;
        /* Make children (canvases) side by side */
        gap: 1rem;
        /* Space between charts */
        width: 100%;
        /* Full width of card body */
        height: 300px;
        /* Height of charts */
    }

    .chart-container canvas {
        flex: 1;
        /* Each canvas takes equal width */
        max-width: 100%;
        /* Prevent overflow */
    }


    /* Modal */
    .modal-content {
        border: none;
        border-radius: var(--card-radius);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    /* Responsive */
    @media (max-width: 768px) {
        #sidebar {
            margin-left: -var(--sidebar-width);
        }

        #content {
            margin-left: 0;
        }

        #sidebar.active {
            margin-left: 0;
        }

        #content.active {
            margin-left: var(--sidebar-width);
        }
    }
</style>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">Disaster Analytics</h4>
            <p class="text-white-50 small mb-0">Admin Dashboard</p>
        </div>

        <ul class="list-unstyled sidebar-menu">
            <li>
                <a href="admin.php" class="nav-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="manage_locations_ajax.php" class="nav-link">
                    <i class="fas fa-water"></i> Manage Locations
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fas fa-sun"></i> Drought Predictions
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fas fa-map-marked-alt"></i> Regional Analysis
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>

        <div class="position-absolute bottom-0 w-100 p-3 text-center">
            <div class="text-white-50 small">Disaster Prediction System v2.0</div>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="content">
        <!-- Header -->
        <header id="header" class="d-flex justify-content-between align-items-center">
            <div>
                <button id="sidebar-toggle" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-bell text-muted"></i>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none text-dark" role="button" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=4361ee&color=fff" class="rounded-circle me-2" width="32" height="32">
                        <span>Admin User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="container-fluid p-4">
            <!-- Stats Overview -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <i class="fas fa-water"></i>
                        <h2 id="total-floods">0</h2>
                        <p>Flood Predictions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <i class="fas fa-sun"></i>
                        <h2 id="total-droughts">0</h2>
                        <p>Drought Predictions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h2 id="high-risk">0</h2>
                        <p>High Risk Events</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <i class="fas fa-chart-line"></i>
                        <h2 id="avg-probability">0%</h2>
                        <p>Average Probability</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Card 1 -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Flood Risk Distribution</span>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="floodChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Drought Risk Distribution</span>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="droughtChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Recent Activities</div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="recent-activities">
                                <li class="list-group-item border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle p-2 me-3">
                                            <i class="fas fa-plus text-white"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">New flood prediction added</p>
                                            <small class="text-muted">2 minutes ago</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle p-2 me-3">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">High risk drought detected</p>
                                            <small class="text-muted">15 minutes ago</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle p-2 me-3">
                                            <i class="fas fa-download text-white"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Report downloaded</p>
                                            <small class="text-muted">1 hour ago</small>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Predictions Tables -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>🌊 Flood Predictions</span>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-container">
                                <table class="table table-hover mb-0" id="flood-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Country</th>
                                            <th>Prediction Date</th>
                                            <th>Horizon</th>
                                            <th>Probability</th>
                                            <th>Risk Level</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Flood data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>🌾 Drought Predictions</span>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-container">
                                <table class="table table-hover mb-0" id="drought-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Country</th>
                                            <th>Prediction Date</th>
                                            <th>Horizon</th>
                                            <th>Probability</th>
                                            <th>Risk Level</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Drought data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="predictionModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Prediction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Overview</h6>
                                    <table class="table table-sm" id="overview-table">
                                        <!-- Overview data will be loaded here -->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="chart-container">
                                <canvas id="timelineChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">Timeline Data</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="details-table">
                            <!-- Timeline data will be loaded here -->
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Download Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        // Fetch data from backend
        async function fetchData() {
            try {
                const res = await fetch("get_predictions.php");
                const data = await res.json();

                const floods = data.floods || [];
                const droughts = data.droughts || [];

                // Update stats
                document.getElementById('total-floods').textContent = floods.length;
                document.getElementById('total-droughts').textContent = droughts.length;

                const highRiskEvents = [...floods, ...droughts].filter(
                    event => event.risk_level === 'High'
                ).length;
                document.getElementById('high-risk').textContent = highRiskEvents;

                const avgProbability = ([...floods, ...droughts].reduce(
                    (sum, event) => sum + parseFloat(event.probability), 0
                ) / (floods.length + droughts.length) * 100).toFixed(1);
                document.getElementById('avg-probability').textContent = avgProbability + '%';

                // Populate tables
                populateTable('flood-table', floods, 'flood');
                populateTable('drought-table', droughts, 'drought');

                // Update charts separately
                updateFloodChart(floods); // Flood chart
                updateDroughtChart(droughts); // Drought chart
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }


        // Populate data tables
        function populateTable(tableId, data, type) {
            const tableBody = document.querySelector(`#${tableId} tbody`);
            tableBody.innerHTML = '';

            data.forEach(item => {
                const row = document.createElement('tr');
                let badgeClass = '';
                switch (item.risk_level) {
                    case 'High':
                        badgeClass = 'bg-danger';
                        break;
                    case 'Medium':
                        badgeClass = 'bg-warning';
                        break;
                    case 'Low':
                        badgeClass = 'bg-success';
                        break;
                }

                row.innerHTML = `
        <td>${item.id}</td>
        <td>${item.country}</td>
        <td>${item.date}</td>
        <td>${item.horizon_days} days</td>
        <td>${(item.probability * 100).toFixed(1)}%</td>
        <td><span class="badge ${badgeClass}">${item.risk_level}</span></td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="showDetails(${item.id}, '${type}')">
            <i class="fas fa-eye me-1"></i> View
          </button>
        </td>
      `;
                tableBody.appendChild(row);
            });
        }

        // Show prediction details in modal
        async function showDetails(id, type) {
            try {
                const url = type === 'flood' ?
                    "get_flood_details.php?id=" + id :
                    "get_drought_details.php?id=" + id;

                const res = await fetch(url);
                const data = await res.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                const prediction = data.prediction;
                const timeline = data.timeline;

                // Update modal title
                document.querySelector('.modal-title').textContent =
                    `${type === 'flood' ? 'Flood' : 'Drought'} Prediction #${id} - ${prediction.country}`;

                // Overview
                const overviewTable = document.getElementById('overview-table');
                overviewTable.innerHTML = `
        <tr><td>Country:</td><td>${prediction.country}</td></tr>
        <tr><td>Prediction Date:</td><td>${prediction.date}</td></tr>
        <tr><td>Horizon:</td><td>${prediction.horizon_days} days</td></tr>
        <tr><td>Probability:</td><td>${(prediction.probability * 100).toFixed(1)}%</td></tr>
        <tr><td>Risk Level:</td><td><span class="badge ${prediction.risk_level === 'High' ? 'bg-danger' : prediction.risk_level === 'Medium' ? 'bg-warning' : 'bg-success'}">${prediction.risk_level}</span></td></tr>
      `;

                // Timeline chart
                const chartCtx = document.getElementById('timelineChart').getContext('2d');
                if (window.timelineChartInstance) window.timelineChartInstance.destroy();

                window.timelineChartInstance = new Chart(chartCtx, {
                    type: 'line',
                    data: {
                        labels: timeline.map(r => r.date),
                        datasets: [{
                            label: 'Probability',
                            data: timeline.map(r => r.flood_probability || r.probability),
                            borderColor: type === 'flood' ? 'rgba(67, 97, 238, 1)' : 'rgba(230, 57, 70, 1)',
                            backgroundColor: type === 'flood' ? 'rgba(67, 97, 238, 0.1)' : 'rgba(230, 57, 70, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 1,
                                ticks: {
                                    callback: function(value) {
                                        return (value * 100) + '%';
                                    }
                                }
                            }
                        }
                    }
                });

                // Details table
                const detailsTable = document.getElementById('details-table');
                detailsTable.innerHTML = '';
                const headerRow = document.createElement('tr');
                Object.keys(timeline[0]).forEach(key => {
                    const th = document.createElement('th');
                    th.textContent = key.replace(/_/g, ' ').toUpperCase();
                    headerRow.appendChild(th);
                });
                detailsTable.appendChild(headerRow);

                timeline.forEach(rowData => {
                    const row = document.createElement('tr');
                    Object.values(rowData).forEach(value => {
                        const td = document.createElement('td');
                        if (typeof value === 'number' && value <= 1 && !Number.isInteger(value)) {
                            td.textContent = (value * 100).toFixed(1) + '%';
                        } else {
                            td.textContent = value;
                        }
                        row.appendChild(td);
                    });
                    detailsTable.appendChild(row);
                });

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('predictionModal'));
                modal.show();

            } catch (error) {
                console.error("Error fetching prediction details:", error);
            }
        }

        // Risk chart update
        // Flood chart
        let floodChart;

        function updateFloodChart(floods) {
            const low = floods.filter(f => f.risk_level === 'Low').length;
            const medium = floods.filter(f => f.risk_level === 'Medium').length;
            const high = floods.filter(f => f.risk_level === 'High').length;

            const ctx = document.getElementById('floodChart').getContext('2d');
            if (floodChart) floodChart.destroy();

            floodChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Low', 'Medium', 'High'],
                    datasets: [{
                        label: 'Flood Predictions',
                        data: [low, medium, high],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 205, 86, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ],
                        borderColor: [
                            'rgb(75, 192, 192)',
                            'rgb(255, 205, 86)',
                            'rgb(255, 99, 132)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Drought chart
        let droughtChart;

        function updateDroughtChart(droughts) {
            const low = droughts.filter(d => d.risk_level === 'Low').length;
            const medium = droughts.filter(d => d.risk_level === 'Medium').length;
            const high = droughts.filter(d => d.risk_level === 'High').length;

            const ctx = document.getElementById('droughtChart').getContext('2d');
            if (droughtChart) droughtChart.destroy();

            droughtChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Low', 'Medium', 'High'],
                    datasets: [{
                        label: 'Drought Predictions',
                        data: [low, medium, high],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ],
                        borderColor: [
                            'rgb(54, 162, 235)',
                            'rgb(255, 206, 86)',
                            'rgb(255, 99, 132)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }



        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            fetchData();
        });
    </script>

</body>

</html>