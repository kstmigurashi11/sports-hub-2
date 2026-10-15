<?php

session_start();

include "database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";

    } else {

        // Find user by email
        $stmt = $conn->prepare(
            "SELECT user_id, full_name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();
        
        

           

            // Check password
            if (password_verify($password, $user["password"])) {

                // Store user information in session
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                // Redirect according to role
                if ($user["role"] == "admin") {

                    header("Location: admin/dashboard.php");
                    exit();

                } else {

                    header("Location: student/dashboard.php");
                    exit();
                }

            } else {

                $message = "Invalid email or password.";
            }

        } else {

            $message = "Invalid email or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - NSBM SportsHub</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="register-container">

    <h1>NSBM SportsHub</h1>

    <h2>Login</h2>

    <?php if (!empty($message)): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form action="login.php" method="POST" onsubmit="return validateLoginForm();" >

        <label>Email</label>

        <input
            type="email"
            name="email"
            id="email"
            placeholder="Enter your email"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter your password"
            required
        >
        

        <button
            type="button"
            onclick="togglePassword('password')"
        >
            Show / Hide Password
        </button>

        <button type="submit">
            Login
        </button>


       

    </form>


    <p>
        Don't have an account?

        <a href="register.php">
            Register
        </a>
    </p>

</div>

<script src="js/script.js"></script>

</body>

</html>

