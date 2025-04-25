<?php
require_once "db.php";

$signupSuccess = false;
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    // Hash the password before saving
    $hashedPassword = password_hash($passwordRaw, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "Email already exists.";
    } else {
        // Insert new user with hashed password
        $insert = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $insert->bind_param("ss", $email, $hashedPassword);
        if ($insert->execute()) {
            $signupSuccess = true;
        } else {
            $errorMessage = "Database error during signup.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Signup</title>
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
      box-shadow: 0 0 30px yellow;
    }
    h2 {
      color: yellow;
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
      background-color: yellow;
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
      color: violet;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Signup</h2>
  <form method="post" action="">
    <input type="email" name="email" placeholder="hello@gmail.com" required><br>
    <input type="password" name="password" placeholder="••••••••" required><br>
    <button type="submit">Signup</button>
  </form>

  <div class="link">
    Already have an account? <a href="login.php">Login here</a>
  </div>
</div>

<?php if ($signupSuccess): ?>
  <script>
    alert('Signup successful!');
    window.location.href = 'login.php';
  </script>
<?php elseif (!empty($errorMessage)): ?>
  <script>
    alert('<?php echo $errorMessage; ?>');
  </script>
<?php endif; ?>

</body>
</html>