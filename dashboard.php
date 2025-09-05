<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
require_once "db.php";

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent predictions (example data - you would replace with actual queries)
$recentPredictions = [
    ['type' => 'Flood', 'risk' => 'High', 'date' => '2023-11-15'],
    ['type' => 'Earthquake', 'risk' => 'Medium', 'date' => '2023-11-10'],
    ['type' => 'Wildfire', 'risk' => 'Low', 'date' => '2023-11-05']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - RescueNet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary: #0d6efd;
      --primary-dark: #0a58ca;
      --secondary: #6c757d;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #198754;
      --warning: #ffc107;
      --danger: #dc3545;
      --card-shadow: 0 4px 12px rgba(0,0,0,0.08);
      --transition: all 0.3s ease;
    }
    
    body { 
      background: #f1f5f9; 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Sidebar Styling */
    .sidebar { 
      background: var(--primary); 
      min-height: 100vh; 
      color: #fff;
      position: fixed;
      width: 250px;
      transition: var(--transition);
      z-index: 1000;
      box-shadow: var(--card-shadow);
    }
    
    .sidebar-header {
      padding: 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar a { 
      color: #fff; 
      text-decoration: none; 
      display: block; 
      padding: 12px 20px;
      border-radius: 6px; 
      margin: 6px 0;
      transition: var(--transition);
      font-weight: 500;
    }
    
    .sidebar a:hover, .sidebar a.active { 
      background: rgba(255,255,255,0.15); 
      transform: translateX(5px);
    }
    
    .sidebar a i {
      margin-right: 10px;
      font-size: 18px;
    }
    
    /* Main Content */
    .main-content {
      margin-left: 250px;
      padding: 20px;
      transition: var(--transition);
    }
    
    /* Cards */
    .card {
      background: #fff; 
      border-radius: 12px; 
      box-shadow: var(--card-shadow);
      border: none;
      transition: var(--transition);
      height: 100%;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .card-header {
      background: transparent;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      font-weight: 600;
      padding: 15px 20px;
    }
    
    .card-body {
      padding: 20px;
    }
    
    /* Action Buttons */
    .action-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 12px 20px;
      border-radius: 10px;
      background: white;
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      box-shadow: var(--card-shadow);
      transition: var(--transition);
      border: 1px solid rgba(0,0,0,0.05);
      text-align: center;
    }
    
    .action-btn:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-3px);
    }
    
    .action-btn i {
      font-size: 24px;
      margin-bottom: 8px;
    }
    
    /* Stats */
    .stat-card {
      text-align: center;
      padding: 15px;
    }
    
    .stat-number {
      font-size: 28px;
      font-weight: 700;
      color: var(--primary);
    }
    
    .stat-label {
      color: var(--secondary);
      font-size: 14px;
    }
    
    /* Risk badges */
    .risk-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .risk-high {
      background: #fee;
      color: var(--danger);
    }
    
    .risk-medium {
      background: #fff3cd;
      color: #856404;
    }
    
    .risk-low {
      background: #d4edda;
      color: var(--success);
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
        text-align: center;
      }
      
      .sidebar .nav-text {
        display: none;
      }
      
      .sidebar a i {
        margin-right: 0;
        font-size: 24px;
      }
      
      .main-content {
        margin-left: 70px;
      }
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    
    <!-- Sidebar -->
    <div class="col-md-2 sidebar p-0">
      <div class="sidebar-header">
        <h4 class="text-center">RescueNet</h4>
      </div>
      <div class="p-3">
        <a href="#" class="active"><i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span></a>
        <a href="templates/home.php"><i class="bi bi-bar-chart-line"></i> <span class="nav-text">Predictions</span></a>
        <a href="#"><i class="bi bi-map"></i> <span class="nav-text">Disaster Map</span></a>
        <a href="tips/index.php"><i class="bi bi-lightbulb"></i> <span class="nav-text">Safety Tips</span></a>
        <a href="#"><i class="bi bi-gear"></i> <span class="nav-text">Settings</span></a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> <span class="nav-text">Logout</span></a>
      </div>
    </div>
    
    <!-- Main Dashboard -->
    <div class="col-md-10 main-content">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold">Welcome, <?php echo htmlspecialchars($user['full_name']); ?> 👋</h2>
          <p class="text-muted">Your personalized disaster prediction dashboard</p>
        </div>
        <div class="d-flex">
          <div class="input-group me-2" style="width: 300px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search...">
          </div>
          <button class="btn btn-primary"><i class="bi bi-bell"></i></button>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div class="row mb-4">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">Quick Actions</h5>
              <div class="row g-3">
                <div class="col-md-3">
                  <a href="python/templates/home.php" class="action-btn">
                    <div>
                      <i class="bi bi-cloud-lightning-rain"></i>
                      <p class="mb-0">Risk Prediction</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="tips/index.php" class="action-btn">
                    <div>
                      <i class="bi bi-lightbulb"></i>
                      <p class="mb-0">Safety Tips</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="#" class="action-btn">
                    <div>
                      <i class="bi bi-map"></i>
                      <p class="mb-0">Disaster Map</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="#" class="action-btn">
                    <div>
                      <i class="bi bi-file-medical"></i>
                      <p class="mb-0">Emergency Plan</p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row g-4">
        
        <!-- Profile & Stats -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">Profile Overview</div>
            <div class="card-body">
              <div class="text-center mb-4">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                  <span class="text-white fw-bold" style="font-size: 32px;"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                </div>
                <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                <p class="text-muted">Member since <?php echo date("M Y", strtotime($user['created_at'] ?? 'now')); ?></p>
              </div>
              
              <div class="row text-center mb-4">
                <div class="col-4">
                  <div class="stat-card">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Predictions</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="stat-card">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Alerts</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="stat-card">
                    <div class="stat-number">5</div>
                    <div class="stat-label">Preparedness</div>
                  </div>
                </div>
              </div>
              
              <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
              <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
              <p><strong>Location:</strong> <?php echo $user['city'].", ".$user['country']; ?></p>
            </div>
          </div>
        </div>
        
        <!-- Recent Predictions -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span>Recent Predictions</span>
              <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
              <?php foreach($recentPredictions as $prediction): ?>
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                  <div>
                    <h6 class="mb-0"><?php echo $prediction['type']; ?></h6>
                    <small class="text-muted"><?php echo $prediction['date']; ?></small>
                  </div>
                  <span class="risk-badge risk-<?php echo strtolower($prediction['risk']); ?>">
                    <?php echo $prediction['risk']; ?> Risk
                  </span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          
          <!-- Disaster Preparedness Tips -->
       
        </div>
        
        <!-- Right Column -->
        <div class="col-md-4">
          
          <div class="card mt-4">
            <div class="card-header">Emergency Contacts</div>
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                  <i class="bi bi-telephone-fill text-white"></i>
                </div>
                <div>
                  <h6 class="mb-0">Local Emergency</h6>
                  <p class="text-muted mb-0">911</p>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                  <i class="bi bi-building text-dark"></i>
                </div>
                <div>
                  <h6 class="mb-0">City Services</h6>
                  <p class="text-muted mb-0">311</p>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                  <i class="bi bi-heart-fill text-white"></i>
                </div>
                <div>
                  <h6 class="mb-0">Red Cross</h6>
                  <p class="text-muted mb-0">1-800-RED-CROSS</p>
                </div>
              </div>
            </div>
          </div>
          
         
        </div>
      </div>
      
      <!-- Recent Activity Section -->
      <!-- <div class="row mt-4">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Recent Activity</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Type</th>
                      <th>Description</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Prediction</td>
                      <td>Flood risk assessment for your area</td>
                      <td>Today, 10:23 AM</td>
                      <td><span class="badge bg-success">Completed</span></td>
                    </tr>
                    <tr>
                      <td>Alert</td>
                      <td>Storm warning notification sent</td>
                      <td>Yesterday, 3:45 PM</td>
                      <td><span class="badge bg-primary">Sent</span></td>
                    </tr>
                    <tr>
                      <td>Preparation</td>
                      <td>Emergency plan updated</td>
                      <td>Nov 12, 2023</td>
                      <td><span class="badge bg-info">Reviewed</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div> -->
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>