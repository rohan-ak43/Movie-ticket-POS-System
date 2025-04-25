<?php
require_once "db.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$loginMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $loginMessage = "Login successful";
            // You can start a session here if needed
        } else {
            $loginMessage = "Invalid credentials";
        }
    } else {
        $loginMessage = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body {
      background-color: black;
      font-family: Arial, sans-serif;
      color: white;
      text-align: center;
    }
    .container {
      background-color: #1a1a1a;
      margin: 100px auto;
      padding: 40px;
      border-radius: 20px;
      width: 300px;
      box-shadow: 0 0 30px violet;
    }
    h2 {
      color: violet;
      margin-bottom: 20px;
    }
    input[type=email], input[type=password] {
      padding: 8px;
      width: 100%;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
    }
    button {
      background-color: violet;
      color: black;
      padding: 10px 20px;
      border: none;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
      border-radius: 5px;
    }
    .link {
      margin-top: 20px;
    }
    a {
      color: yellow;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Login</h2>
  <form method="post" action="">
    <input type="email" name="email" placeholder="Enter your email" required><br>
    <input type="password" name="password" placeholder="Enter your password" required><br>
    <button type="submit">Login</button>
  </form>

  <div class="link">
    Don't have an account? <a href="signup.php">Signup here</a>
  </div>
</div>

<?php if (!empty($loginMessage)): ?>
  <script>
    alert('<?php echo $loginMessage; ?>');
    <?php if ($loginMessage === "Login successful"): ?>
      window.location.href = 'booking.php'; // or wherever you want to redirect
    <?php endif; ?>
  </script>
<?php endif; ?>

</body>
</html>