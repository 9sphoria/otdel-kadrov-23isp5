<?php
session_start();

// Подключение к БД
$dsn = "mysql:host=localhost;dbname=hr_agency;charset=utf8mb4";
$user = "root"; 
$pass = ""; 

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && hash("sha256", $password) === $user["password_hash"]) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        header("Location: home.php");
        exit;
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Авторизация - Отдел кадров</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:400px; margin-top:80px;">
  <h2 style="text-align:center;">Вход</h2>
  <?php if (!empty($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
  <form method="post">
    <label>Логин:</label>
    <input type="text" name="login" required>
    <label>Пароль:</label>
    <input type="password" name="password" required>
    <input type="submit" value="Войти">
  </form>
</div>
</body>
</html>
