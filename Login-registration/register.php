<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <?php
            include("php/config.php");
            if (isset($_POST['submit'])) {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                // email format validation
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<div class='message'>
                            <p>Invalid email format. Please enter a valid email address.</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                    exit;
                }

                // Password confirmation and strength check
                if ($password !== $confirm_password || strlen($password) < 8 || !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
                    echo "<div class='message'>
                            <p>Passwords do not match or don't meet the requirements.</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                    exit;
                }

                // CAPTCHA check - 
                $recaptcha_secret = "6Ld65kIpAAAAABgmBF9GBFAWYvU-0wXwa3yqz7wY";
                $captcha = $_POST['g-recaptcha-response'];

                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$captcha");
                $responseKeys = json_decode($response, true);

                if (intval($responseKeys["success"]) !== 1) {
                    echo "<div class='message'>
                            <p>CAPTCHA verification failed.</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                    exit;
                }

                // Proceed with registration
                $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");

                if (mysqli_num_rows($verify_query) != 0) {
                    echo "<div class='message'>
                            <p>This email is used, Try another one, please!</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    mysqli_query($con, "INSERT INTO users(Username, Email, Password) VALUES('$username','$email','$hashed_password')") or die("Error Occurred");

                    echo "<div class='message'>
                            <p>Registration successful!</p>
                          </div> <br>";
                    echo "<a href='index.php'><button class='btn'>Login Now</button>";
                }
            } else {
            ?>
                <header>Sign Up</header>
                <form action="" method="post">
                    <div class="field input">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" autocomplete="off" required>
                    </div>

                    <div class="field input">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" autocomplete="off" required>
                    </div>
                    <div class="field input">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" autocomplete="off" required>
                    </div>
                    <div class="field input">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" autocomplete="off" required>
                    </div>

                    <div class="field password-details">
                        <label>Password Strength:</label>
                        <div id="password-strength"></div>
                    </div>

                    <div class="field">
                        <!-- CAPTCHA widget -->
                        <div class="g-recaptcha" data-sitekey="6Ld65kIpAAAAALgTVTkK83iUopTISGXF_8eBnJbH"></div>
                    </div>

                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="Register" required>
                    </div>
                    <div class="links">
                        Already have an account? <a href="index.php">Sign In</a>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
    <!-- reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        const passwordField = document.getElementById('password');
        const passwordStrength = document.getElementById('password-strength');

        passwordField.addEventListener('input', function () {
            const password = this.value;
            let strength = 0;

            // Check for uppercase letters
            if (password.match(/[A-Z]+/)) {
                strength += 1;
            }

            // Check for lowercase letters
            if (password.match(/[a-z]+/)) {
                strength += 1;
            }

            // Check for special characters
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) {
                strength += 1;
            }

            // Check length
            if (password.length >= 8) {
                strength += 1;
            }

            // Update the indicator based on the strength
            switch (strength) {
                case 0:
                case 1:
                    passwordStrength.innerHTML = 'Weak';
                    break;
                case 2:
                    passwordStrength.innerHTML = 'Moderate';
                    break;
                case 3:
                    passwordStrength.innerHTML = 'Good';
                    break;
                case 4:
                    passwordStrength.innerHTML = 'Strong';
                    break;
                default:
                    passwordStrength.innerHTML = '';
            }
        });
    </script>
</body>
</html>
