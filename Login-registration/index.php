
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Login</title>
    <!--  reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <div class="box form-box">
        <?php
session_start();
include("php/config.php");
if (isset($_POST['submit'])) {
    // Verify reCAPTCHA
    $recaptcha_secret = "6Ld65kIpAAAAABgmBF9GBFAWYvU-0wXwa3yqz7wY";
    $captcha = $_POST['g-recaptcha-response'];

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$captcha");
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        echo "<div class='message'>
                <p>CAPTCHA verification failed.</p>
              </div> <br>";
        echo "<a href='index.php'><button class='btn'>Go Back</button>";
        exit;
    }

    // Proceed with login verification
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $result = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'") or die("Select Error");
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['PASSWORD'])) {
        $_SESSION['valid'] = $row['Email'];
        $_SESSION['username'] = $row['Username'];
        $_SESSION['id'] = $row['Id'];
        header("Location: home.php");
        exit;
    } else {
        echo "<div class='message'>
                <p>Wrong Email or Password</p>
              </div> <br>";
        
    }
}
?>
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <!--  reCAPTCHA widget -->
                <div class="field">
                    <div class="g-recaptcha" data-sitekey="6Ld65kIpAAAAALgTVTkK83iUopTISGXF_8eBnJbH"></div>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="links">
                    Don't have an account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>