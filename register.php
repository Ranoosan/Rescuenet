<?php
session_start();
require_once "db.php";

$message = "";

// Fetch countries
try {
    $stmt = $pdo->query("SELECT DISTINCT country_name FROM asian_locations ORDER BY country_name ASC");
    $countries = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $countries = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['step']) && $_POST['step'] == 1) {
        $_SESSION['step1'] = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'country' => trim($_POST['country']),
            'city' => trim($_POST['city']),
            'address' => trim($_POST['address']),
            'age' => trim($_POST['age']),
            'gender' => trim($_POST['gender'])
        ];
        header("Location: register.php?step=2");
        exit();
    }

    if(isset($_POST['step']) && $_POST['step'] == 2) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if(strlen($password) < 8) $message = "Password must be at least 8 characters.";
        elseif($password !== $confirm_password) $message = "Passwords do not match.";
        else {
            $step1 = $_SESSION['step1'];
            $hashed_pass = password_hash($password, PASSWORD_BCRYPT);

            try {
                $stmt = $pdo->prepare("INSERT INTO users 
                (full_name,email,phone,username,password,country,city,address,age,gender) 
                VALUES (?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute([
                    $step1['full_name'],
                    $step1['email'],
                    $step1['phone'],
                    $username,
                    $hashed_pass,
                    $step1['country'],
                    $step1['city'],
                    $step1['address'],
                    $step1['age'] ? $step1['age'] : NULL,
                    $step1['gender'] ? $step1['gender'] : NULL
                ]);
                unset($_SESSION['step1']);
                $message = "Registration successful. You can now login.";
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    }
}

$step = isset($_GET['step']) ? $_GET['step'] : 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - RescueNet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-container {
            max-width: 1100px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .register-left {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-right {
            background: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .feature-list i {
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .form-control {
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .input-group-text {
            background: white;
            border-right: none;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--success);
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-success:hover {
            background: #15803d;
            transform: translateY(-2px);
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 15px;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 8px;
            transition: var(--transition);
        }

        .step.active .step-number {
            background: var(--primary);
            color: white;
        }

        .step.completed .step-number {
            background: var(--success);
            color: white;
        }

        .step-text {
            font-size: 14px;
            color: var(--secondary);
        }

        .step.active .step-text {
            color: var(--primary);
            font-weight: 600;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e2e8f0;
            margin: 0 10px;
            position: relative;
            top: -20px;
        }

        .error-message {
            color: var(--danger);
            background: #fef2f2;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-message {
            color: var(--success);
            background: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary);
        }

        .password-container {
            position: relative;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            margin-top: 8px;
        }

        @media (max-width: 992px) {
            .register-left {
                display: none;
            }
            
            .register-right {
                border-radius: 20px;
            }
        }

        @media (max-width: 576px) {
            .step-indicator {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .step-line {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="row g-0">
        <!-- Left side with branding and features -->
        <div class="col-lg-6 register-left">
            <div class="brand">RescueNet</div>
            <h2>Join our disaster preparedness community</h2>
            <p>Create your account to access predictions, safety tips, and emergency plans</p>
            
            <ul class="feature-list">
                <li>
                    <i class="fas fa-cloud-lightning-rain"></i>
                    <span>Real-time disaster predictions</span>
                </li>
                <li>
                    <i class="fas fa-lightbulb"></i>
                    <span>Personalized safety recommendations</span>
                </li>
                <li>
                    <i class="fas fa-map"></i>
                    <span>Interactive disaster maps</span>
                </li>
                <li>
                    <i class="fas fa-shield-check"></i>
                    <span>Emergency preparedness plans</span>
                </li>
            </ul>
            
            <div class="mt-auto">
                <p class="mb-0">Already have an account? <a href="login.php" class="text-white">Login here</a></p>
            </div>
        </div>
        
        <!-- Right side with registration form -->
        <div class="col-lg-6 register-right">
            <div class="w-100" style="max-width: 500px; margin: 0 auto;">
                <h2 class="mb-1">Create Account</h2>
                <p class="text-muted mb-4">Join RescueNet to access disaster prediction tools</p>
                
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step <?php echo $step == 1 ? 'active' : 'completed'; ?>">
                        <div class="step-number">1</div>
                        <div class="step-text">Personal Info</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step <?php echo $step == 2 ? 'active' : ($step == 1 ? '' : 'completed'); ?>">
                        <div class="step-number">2</div>
                        <div class="step-text">Account Setup</div>
                    </div>
                </div>
                
                <?php if($message): ?>
                <div class="<?php echo strpos($message, 'successful') !== false ? 'success-message' : 'error-message'; ?>">
                    <i class="fas <?php echo strpos($message, 'successful') !== false ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <span><?php echo $message; ?></span>
                </div>
                <?php endif; ?>
                
                <?php if($step == 1): ?>
                <form method="POST" onsubmit="return validateStep1();">
                    <input type="hidden" name="step" value="1">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-phone"></i> Phone Number *</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-flag"></i> Country *</label>
                            <select class="form-select" id="country" name="country" required>
                                <option value="">Select Country</option>
                                <?php foreach($countries as $c): ?>
                                <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-city"></i> City/Village *</label>
                            <select class="form-select" id="city" name="city" required>
                                <option value="">Select city</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-map-marker-alt"></i> Exact Address / Coordinates</label>
                        <input type="text" class="form-control" name="address" placeholder="Street address or GPS coordinates">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-birthday-cake"></i> Age</label>
                            <input type="number" class="form-control" name="age" min="1" placeholder="Optional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select gender (optional)</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-arrow-right me-2"></i> Continue to Account Setup
                    </button>
                </form>

                <?php elseif($step == 2): ?>
                <form method="POST" onsubmit="return validateStep2();">
                    <input type="hidden" name="step" value="2">
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-user-circle"></i> Username *</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Choose a username">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-lock"></i> Password *</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Create a strong password">
                            <span class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="progress mt-2">
                            <div id="password-strength" class="progress-bar" role="progressbar" style="width:0%"></div>
                        </div>
                        <small id="password-strength-text" class="text-muted">Password strength</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label"><i class="fas fa-lock"></i> Confirm Password *</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                            <span class="password-toggle" id="confirmPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                    
                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php" class="text-decoration-none fw-semibold">Login here</a>
                    </p>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function validateStep1() {
    const full_name = $('#full_name').val().trim();
    const email = $('#email').val().trim();
    const phone = $('#phone').val().trim();
    const country = $('#country').val();
    const city = $('#city').val();

    if(!full_name || !email || !phone || !country || !city){
        alert("Please fill all required fields.");
        return false;
    }

    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,6}$/;
    if(!emailPattern.test(email)) {
        alert("Invalid email format.");
        return false;
    }

    const phonePattern = /^[0-9]{7,15}$/;
    if(!phonePattern.test(phone)) {
        alert("Invalid phone number (7-15 digits).");
        return false;
    }
    return true;
}

function validateStep2() {
    const username = $('#username').val().trim();
    const password = $('#password').val();
    const confirm_password = $('#confirm_password').val();

    if(!username || !password || !confirm_password){
        alert("Please fill all required fields.");
        return false;
    }
    if(password.length < 8){
        alert("Password must be at least 8 characters.");
        return false;
    }
    if(password !== confirm_password){
        alert("Passwords do not match.");
        return false;
    }
    return true;
}

function checkPasswordStrength(password){
    let strength = 0;
    if(password.length >= 8) strength++;
    if(password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if(password.match(/[0-9]/)) strength++;
    if(password.match(/[\W]/)) strength++;
    return strength;
}

$(document).ready(function(){
    // Toggle password visibility
    $('#passwordToggle').click(function() {
        const passwordInput = $('#password');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        
        const eyeIcon = $(this).find('i');
        if (type === 'password') {
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });
    
    $('#confirmPasswordToggle').click(function() {
        const passwordInput = $('#confirm_password');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        
        const eyeIcon = $(this).find('i');
        if (type === 'password') {
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    $('#country').change(function(){
        const country = $(this).val();
        if(country){
            $.ajax({
                url: 'fetch_cities.php',
                type: 'POST',
                data: {country: country},
                success: function(data){
                    $('#city').html(data);
                }
            });
        } else {
            $('#city').html('<option value="">Select city</option>');
        }
    });

    $('#password').on('input', function(){
        const val = $(this).val();
        const strength = checkPasswordStrength(val);
        const meter = $('#password-strength');
        const text = $('#password-strength-text');

        if(strength <= 1){
            meter.removeClass().addClass('bg-danger').css('width','25%');
            text.text('Weak');
        } else if(strength == 2){
            meter.removeClass().addClass('bg-warning').css('width','50%');
            text.text('Medium');
        } else if(strength == 3){
            meter.removeClass().addClass('bg-info').css('width','75%');
            text.text('Strong');
        } else {
            meter.removeClass().addClass('bg-success').css('width','100%');
            text.text('Very Strong');
        }
    });
});
</script>
</body>
</html>