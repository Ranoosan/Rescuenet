<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
require_once "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Prediction Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --light: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            color: #334155;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            text-align: center;
        }

        .header {
            margin-bottom: 60px;
            animation: fadeIn 0.8s ease-out;
        }

        .logo {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 1.2rem;
            color: var(--secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--primary);
            padding: 30px 25px;
            width: 280px;
            border-radius: 16px;
            text-decoration: none;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid #e2e8f0;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .btn-flood:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-drought:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .btn:hover .icon {
            transform: scale(1.1);
        }

        .btn-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .btn-desc {
            font-size: 0.95rem;
            color: var(--secondary);
            line-height: 1.5;
        }

        .btn:hover .btn-desc {
            color: rgba(255, 255, 255, 0.9);
        }

        .footer {
            margin-top: 60px;
            color: var(--secondary);
            font-size: 0.9rem;
            animation: fadeIn 1.2s ease-out;
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

        /* Responsive design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.2rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .button-container {
                gap: 20px;
            }
            
            .btn {
                width: 100%;
                max-width: 320px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }
            
            .logo {
                font-size: 2rem;
            }
            
            .btn-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Disaster Prediction Platform</h1>
            <p class="subtitle">Advanced predictive analytics for natural disaster monitoring and early warning systems</p>
        </div>

        <div class="button-container">
            <a href="http://127.0.0.1:5000/" class="btn btn-flood">
                <div class="icon">
                    <i class="fas fa-water"></i>
                </div>
                <div class="btn-title">Flood Prediction</div>
                <p class="btn-desc">Real-time flood risk assessment and water level monitoring</p>
            </a>
            
            <a href="http://127.0.0.1:5001/" class="btn btn-drought">
                <div class="icon">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="btn-title">Drought Prediction</div>
                <p class="btn-desc">Drought condition analysis and water resource forecasting</p>
            </a>
        </div>

        <div class="footer">
            <p>© 2023 Disaster Prediction Platform | Advanced Monitoring System</p>
        </div>
    </div>

    <script>
        // Add subtle interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            
            buttons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>