<?php 
session_start();
require_once "db.php";

// Fetch statistics from database
$stats = [
    'total_predictions' => 0,
    'recent_predictions' => 0,
    'active_alerts' => 0,
    'preparedness_items' => 0
];

try {
    // Get total predictions count
    $flood_count = $pdo->query("SELECT COUNT(*) FROM flood_predictions")->fetchColumn();
    $drought_count = $pdo->query("SELECT COUNT(*) FROM drought_predictions")->fetchColumn();
    $stats['total_predictions'] = $flood_count + $drought_count;
    
    // Get recent predictions (last 7 days)
    $flood_recent = $pdo->query("SELECT COUNT(*) FROM flood_predictions WHERE date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
    $drought_recent = $pdo->query("SELECT COUNT(*) FROM drought_predictions WHERE date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
    $stats['recent_predictions'] = $flood_recent + $drought_recent;
    
    // Get active alerts (high risk predictions)
    $flood_alerts = $pdo->query("SELECT COUNT(*) FROM flood_predictions WHERE risk_level = 'High'")->fetchColumn();
    $drought_alerts = $pdo->query("SELECT COUNT(*) FROM drought_predictions WHERE risk_level = 'High'")->fetchColumn();
    $stats['active_alerts'] = $flood_alerts + $drought_alerts;
    
    // Get preparedness items (emergency contacts)
    $stats['preparedness_items'] = $pdo->query("SELECT COUNT(*) FROM emergency_contacts_advanced")->fetchColumn();
    
    // Get recent predictions for display
    $recent_predictions = [];
    $flood_results = $pdo->query("SELECT 'Flood' as type, country, date, flood_probability as probability, risk_level 
                                 FROM flood_predictions 
                                 ORDER BY date DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    $drought_results = $pdo->query("SELECT 'Drought' as type, country, date, probability, risk_level 
                                   FROM drought_predictions 
                                   ORDER BY date DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    $recent_predictions = array_merge($flood_results, $drought_results);
    usort($recent_predictions, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    $recent_predictions = array_slice($recent_predictions, 0, 5);
    
} catch (PDOException $e) {
    // If there's an error, we'll use default values
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RescueNet - Smart Disaster Platform</title>
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
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f8f9fa;
      color: #333;
      line-height: 1.6;
    }
    
    /* Navigation */
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
      padding: 15px 0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      transition: var(--transition);
    }
    
    .navbar-brand {
      font-weight: 800;
      font-size: 1.8rem;
      color: var(--primary);
    }
    
    .navbar-nav .nav-link {
      font-weight: 500;
      color: var(--dark);
      margin: 0 10px;
      transition: var(--transition);
    }
    
    .navbar-nav .nav-link:hover {
      color: var(--primary);
    }
    
    .btn-nav {
      padding: 8px 20px;
      border-radius: 30px;
      font-weight: 600;
      transition: var(--transition);
    }
    
    /* Hero Section */
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1593113630400-ea4288922497?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') center/cover no-repeat;
      height: 100vh;
      color: #fff;
      display: flex;
      align-items: center;
      text-align: center;
    }
    
    .hero-content {
      max-width: 800px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 20px;
    }
    
    .hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 40px;
      opacity: 0.9;
    }
    
    .hero-buttons .btn {
      margin: 0 10px;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1.1rem;
    }
    
    /* Features */
    .section-title {
      text-align: center;
      margin-bottom: 50px;
      font-weight: 700;
      color: var(--dark);
    }
    
    .feature-card {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: var(--card-shadow);
      transition: var(--transition);
      height: 100%;
      text-align: center;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .feature-icon {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 20px;
    }
    
    /* Stories Section */
    .story-card {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--card-shadow);
      transition: var(--transition);
      height: 100%;
    }
    
    .story-card:hover {
      transform: translateY(-5px);
    }
    
    .story-img {
      height: 200px;
      object-fit: cover;
    }
    
    .story-content {
      padding: 20px;
    }
    
    /* Advice Cards */
    .advice-card {
      background: #fff;
      border-left: 4px solid var(--primary);
      border-radius: 8px;
      padding: 20px;
      box-shadow: var(--card-shadow);
      margin-bottom: 20px;
    }
    
    /* Stats Section */
    .stats-section {
      background: var(--primary);
      color: white;
      padding: 80px 0;
    }
    
    .stat-item {
      text-align: center;
    }
    
    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    /* Prediction Table */
    .prediction-table {
      font-size: 0.9rem;
    }
    
    .prediction-table .badge {
      font-size: 0.75em;
    }
    
    /* Footer */
    footer {
      background: var(--dark);
      color: #adb5bd;
      padding: 60px 0 30px;
    }
    
    .footer-links h5 {
      color: #fff;
      margin-bottom: 20px;
      font-weight: 600;
    }
    
    .footer-links ul {
      list-style: none;
      padding: 0;
    }
    
    .footer-links li {
      margin-bottom: 10px;
    }
    
    .footer-links a {
      color: #adb5bd;
      text-decoration: none;
      transition: var(--transition);
    }
    
    .footer-links a:hover {
      color: #fff;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1.2rem;
      }
      
      .hero-buttons .btn {
        display: block;
        margin: 10px auto;
        width: 200px;
      }
    }
  </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container">
    <a class="navbar-brand" href="#">RescueNet</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="#stats">Statistics</a></li>
        <li class="nav-item"><a class="nav-link" href="#predictions">Predictions</a></li>
        <li class="nav-item"><a class="nav-link" href="#advice">Advice</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
      </ul>
      <div class="d-flex">
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="dashboard.php" class="btn btn-outline-primary btn-nav me-2">Dashboard</a>
          <a href="logout.php" class="btn btn-primary btn-nav">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary btn-nav me-2">Login</a>
          <a href="register.php" class="btn btn-primary btn-nav">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <h1 class="hero-title">RescueNet</h1>
      <p class="hero-subtitle">Smart Disaster Prediction, Response & Relief Platform</p>
      <div class="hero-buttons">
        <a href="#features" class="btn btn-primary btn-lg">Learn More</a>
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="dashboard.php" class="btn btn-outline-light btn-lg">My Dashboard</a>
        <?php else: ?>
          <a href="register.php" class="btn btn-outline-light btn-lg">Get Started</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5" id="features">
  <div class="container">
    <h2 class="section-title">Our Features</h2>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="feature-card">
          <i class="bi bi-cloud-lightning feature-icon"></i>
          <h5>Disaster Prediction</h5>
          <p>AI-powered real-time flood, drought, cyclone, and landslide predictions with 90%+ accuracy.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <i class="bi bi-heart-pulse feature-icon"></i>
          <h5>Blood Donation</h5>
          <p>Integrated emergency blood donation system with live donor matching and tracking.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <i class="bi bi-broadcast feature-icon"></i>
          <h5>Alerts & Warnings</h5>
          <p>Receive SMS, Email, and App alerts tailored to your location and disaster type.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <i class="bi bi-telephone feature-icon"></i>
          <h5>Emergency Contacts</h5>
          <p>Access local rescue agencies, hospitals, and helplines instantly during emergencies.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats-section" id="stats">
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="stat-item">
          <div class="stat-number"><?php echo $stats['total_predictions']; ?>+</div>
          <p>Total Predictions</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-item">
          <div class="stat-number"><?php echo $stats['recent_predictions']; ?>+</div>
          <p>Recent Predictions</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-item">
          <div class="stat-number"><?php echo $stats['active_alerts']; ?>+</div>
          <p>Active Alerts</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-item">
          <div class="stat-number"><?php echo $stats['preparedness_items']; ?>+</div>
          <p>Preparedness Resources</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Recent Predictions Section -->
<section class="py-5 bg-light" id="predictions">
  <div class="container">
    <h2 class="section-title">Recent Predictions</h2>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="card-title mb-0">Latest Disaster Predictions</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover prediction-table">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Country</th>
                    <th>Date</th>
                    <th>Probability</th>
                    <th>Risk Level</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($recent_predictions)): ?>
                    <?php foreach($recent_predictions as $prediction): ?>
                      <tr>
                        <td>
                          <span class="badge bg-<?php 
                            echo $prediction['type'] == 'Flood' ? 'primary' : 'warning text-dark'; 
                          ?>">
                            <?php echo $prediction['type']; ?>
                          </span>
                        </td>
                        <td><?php echo htmlspecialchars($prediction['country']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($prediction['date'])); ?></td>
                        <td><?php echo $prediction['probability']; ?>%</td>
                        <td>
                          <span class="badge bg-<?php 
                            if($prediction['risk_level'] == 'High') echo 'danger';
                            elseif($prediction['risk_level'] == 'Medium') echo 'warning';
                            else echo 'success';
                          ?>">
                            <?php echo $prediction['risk_level']; ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">No recent predictions available</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="text-center mt-3">
              <a href="predictions.php" class="btn btn-outline-primary">View All Predictions</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Stories Section -->
<section class="py-5" id="stories">
  <div class="container">
    <h2 class="section-title">Success Stories</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="story-card">
          <img src="https://images.unsplash.com/photo-1589652717521-10c0d092dea9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="story-img w-100" alt="Flood rescue">
          <div class="story-content">
            <h5>Flood Prediction Saved Hundreds</h5>
            <p>Our system accurately predicted the 2023 Bangladesh floods 72 hours in advance, allowing for timely evacuations.</p>
            <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="story-card">
          <img src="https://images.unsplash.com/photo-1615876234886-fd9a39fda97f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="story-img w-100" alt="Drought management">
          <div class="story-content">
            <h5>Drought Management in Africa</h5>
            <p>Early drought warnings helped Kenyan farmers implement water conservation strategies, saving crops.</p>
            <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="story-card">
          <img src="https://images.unsplash.com/photo-1593113630400-ea4288922497?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="story-img w-100" alt="Emergency response">
          <div class="story-content">
            <h5>Cyclone Emergency Response</h5>
            <p>During Cyclone Amphan, our platform coordinated rescue efforts and helped locate stranded families.</p>
            <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Advice Section -->
<section class="py-5 bg-light" id="advice">
  <div class="container">
    <h2 class="section-title">Disaster Preparedness Advice</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="advice-card">
          <h5><i class="bi bi-cloud-rain me-2"></i> Flood Safety</h5>
          <p>Move to higher ground immediately during flood warnings. Avoid walking through moving water. Do not drive in flooded areas.</p>
        </div>
        <div class="advice-card">
          <h5><i class="bi bi-wind me-2"></i> Cyclone Preparedness</h5>
          <p>Secure your home, cover windows, and identify a safe room. Prepare an emergency kit with supplies for at least 3 days.</p>
        </div>
        <div class="advice-card">
          <h5><i class="bi bi-droplet me-2"></i> Drought Management</h5>
          <p>Collect rainwater, fix leaks, and use water-efficient irrigation. Plant drought-resistant crops and trees.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="advice-card">
          <h5><i class="bi bi-geo me-2"></i> Landslide Safety</h5>
          <p>Stay alert during heavy rains. Listen for unusual sounds that might indicate moving debris. Evacuate if you're in a susceptible area.</p>
        </div>
        <div class="advice-card">
          <h5><i class="bi bi-shield me-2"></i> Emergency Kit</h5>
          <p>Always have water, non-perishable food, flashlight, batteries, first aid kit, medications, and important documents ready.</p>
        </div>
        <div class="advice-card">
          <h5><i class="bi bi-lightning me-2"></i> Earthquake Safety</h5>
          <p>Drop, cover, and hold on during earthquakes. Stay away from windows, and if outdoors, find a clear spot away from buildings.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Prediction Technology -->
<section class="py-5">
  <div class="container">
    <h2 class="section-title">Our Prediction Technology</h2>
    <div class="row align-items-center">
      <div class="col-md-6">
        <h3>How Our AI Predicts Disasters</h3>
        <p>RescueNet uses advanced machine learning algorithms that analyze historical data, weather patterns, satellite imagery, and real-time sensor data to predict disasters with unprecedented accuracy.</p>
        <ul class="list-group list-group-flush">
          <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Real-time data processing from 50+ sources</li>
          <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Predictive analytics with 92% accuracy rate</li>
          <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Customized alerts based on location and risk profile</li>
          <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Continuous learning from new disaster events</li>
        </ul>
      </div>
      <div class="col-md-6">
        <img src="https://images.unsplash.com/photo-1555949963-aa79dcee981c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="img-fluid rounded shadow" alt="Data analysis">
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer id="about">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h5>RescueNet</h5>
        <p>Smart Disaster Prediction & Relief Platform</p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" class="text-light"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-light"><i class="bi bi-twitter"></i></a>
          <a href="#" class="text-light"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-light"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
      <div class="col-md-2 footer-links">
        <h5>Quick Links</h5>
        <ul>
          <li><a href="#">Home</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#predictions">Predictions</a></li>
          <li><a href="#advice">Advice</a></li>
        </ul>
      </div>
      <div class="col-md-2 footer-links">
        <h5>Services</h5>
        <ul>
          <li><a href="predictions.php">Predictions</a></li>
          <li><a href="alerts.php">Alerts</a></li>
          <li><a href="blood_bank.php">Blood Bank</a></li>
          <li><a href="resources.php">Resources</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h5>Subscribe to Our Alerts</h5>
        <p>Get important disaster warnings and updates</p>
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Your Email">
          <button class="btn btn-primary" type="button">Subscribe</button>
        </div>
      </div>
    </div>
    <hr class="mt-4 mb-4">
    <p class="text-center">© 2025 RescueNet | All Rights Reserved</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
      navbar.style.padding = '10px 0';
      navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
    } else {
      navbar.style.padding = '15px 0';
      navbar.style.boxShadow = 'none';
    }
  });
  
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
</script>
</body>
</html>