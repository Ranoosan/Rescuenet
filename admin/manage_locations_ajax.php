<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Asian Locations Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #4361ee;
    --secondary: #3f37c9;
    --card-radius: 12px;
    --transition: all 0.3s ease;
    --sidebar-width: 260px;
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
    padding-bottom: 70px;
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
    padding: 2rem;
    transition: var(--transition);
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

/* Buttons */
.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-weight: 500;
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
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0">Disaster Analytics</h4>
        <p class="text-white-50 small mb-0">Admin Dashboard</p>
    </div>
    <ul class="list-unstyled sidebar-menu">
        <li><a href="admin.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="manage_locations_ajax.php" class="nav-link"><i class="fas fa-water"></i> Manage Locations</a></li>
        <li><a href="#" class="nav-link"><i class="fas fa-sun"></i> Drought Predictions</a></li>
        <li><a href="#" class="nav-link"><i class="fas fa-map-marked-alt"></i> Regional Analysis</a></li>
        <li><a href="#" class="nav-link"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
    <div class="position-absolute bottom-0 w-100 p-3 text-center">
        <div class="text-white-50 small">Disaster Prediction System v2.0</div>
    </div>
</nav>

<!-- Main Content -->
<div id="content">
    <div class="container-fluid">
        <h2 class="mb-4">Asian Locations Management (AJAX)</h2>

        <!-- Add/Edit Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="locationForm">
                    <input type="hidden" name="id" id="locationId">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Country Name</label>
                            <input type="text" name="country_name" id="countryName" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">ISO Code</label>
                            <input type="text" name="country_iso" id="countryISO" class="form-control" maxlength="3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City Name</label>
                            <input type="text" name="city_name" id="cityName" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isCapital" name="is_capital">
                                <label class="form-check-label">Capital</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" id="notes" class="form-control">
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-success" id="saveBtn">Add Location</button>
                            <button type="button" class="btn btn-primary d-none" id="updateBtn">Update Location</button>
                            <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Locations Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered" id="locationsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Country</th>
                            <th>ISO</th>
                            <th>City</th>
                            <th>Capital</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
async function fetchLocations() {
    const res = await fetch('locations_api.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#locationsTable tbody');
    tbody.innerHTML = '';
    data.forEach(loc => {
        tbody.innerHTML += `
        <tr>
          <td>${loc.id}</td>
          <td>${loc.country_name}</td>
          <td>${loc.country_iso ?? ''}</td>
          <td>${loc.city_name}</td>
          <td>${loc.is_capital ? 'Yes' : 'No'}</td>
          <td>${loc.notes ?? ''}</td>
          <td>
            <button class="btn btn-sm btn-primary" onclick='editLocation(${JSON.stringify(loc)})'>Edit</button>
            <button class="btn btn-sm btn-danger" onclick='deleteLocation(${loc.id})'>Delete</button>
          </td>
        </tr>`;
    });
}

async function addLocation(data) {
    const res = await fetch('locations_api.php?action=add', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    return res.json();
}

async function updateLocation(data) {
    const res = await fetch('locations_api.php?action=update', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    return res.json();
}

async function deleteLocation(id) {
    if (!confirm('Delete this location?')) return;
    const res = await fetch('locations_api.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id})
    });
    await res.json();
    fetchLocations();
}

// Form events
document.getElementById('locationForm').addEventListener('submit', async e => {
    e.preventDefault();
    const data = {
        id: document.getElementById('locationId').value,
        country_name: document.getElementById('countryName').value,
        country_iso: document.getElementById('countryISO').value,
        city_name: document.getElementById('cityName').value,
        is_capital: document.getElementById('isCapital').checked ? 1 : 0,
        notes: document.getElementById('notes').value
    };
    if (document.getElementById('saveBtn').classList.contains('d-none')) {
        await updateLocation(data);
    } else {
        await addLocation(data);
    }
    cancelEdit();
    fetchLocations();
});

// Edit location
function editLocation(loc) {
    document.getElementById('locationId').value = loc.id;
    document.getElementById('countryName').value = loc.country_name;
    document.getElementById('countryISO').value = loc.country_iso ?? '';
    document.getElementById('cityName').value = loc.city_name;
    document.getElementById('isCapital').checked = loc.is_capital == 1;
    document.getElementById('notes').value = loc.notes ?? '';

    document.getElementById('saveBtn').classList.add('d-none');
    document.getElementById('updateBtn').classList.remove('d-none');
}

// Cancel edit
function cancelEdit() {
    document.getElementById('locationForm').reset();
    document.getElementById('locationId').value = '';
    document.getElementById('saveBtn').classList.remove('d-none');
    document.getElementById('updateBtn').classList.add('d-none');
}
document.getElementById('cancelBtn').addEventListener('click', cancelEdit);

// Initial fetch
fetchLocations();
</script>

</body>
</html>
