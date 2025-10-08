<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Главная - Отдел кадров</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>Автоматизированная система "Отдел кадров"</header>
  <nav>
    <a href="home.php">Главная</a>
    <a href="seekers.php">Соискатели</a>
    <a href="resumes.php">Резюме</a>
    <a href="vacancies.php">Вакансии</a>
    <a href="applications.php">Отклики</a>
    <a href="placements.php">Трудоустройства</a>
    <a href="logout.php">Выход</a>
  </nav>
  <div class="container" style="text-align:center;">
    <h2>Добро пожаловать!</h2>
    <p>Вы вошли как <b><?=htmlspecialchars($_SESSION["role"]) ?></b>.</p>
    <p>Используйте меню выше для работы с системой.</p>
  </div>
</body>
</html>
