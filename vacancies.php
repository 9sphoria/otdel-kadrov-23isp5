<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: index.php"); exit; }

$dsn="mysql:host=localhost;dbname=hr_agency;charset=utf8mb4";
$pdo=new PDO($dsn,"root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["add"])) {
  $pdo->exec("INSERT IGNORE INTO employers (id,name) VALUES (1,'Работодатель по умолчанию')");
  $stmt=$pdo->prepare("INSERT INTO vacancies(employer_id,title,description,salary_from,salary_to) VALUES (1,?,?,?,?)");
  $stmt->execute([$_POST["title"],$_POST["description"],$_POST["salary_from"],$_POST["salary_to"]]);
  header("Location: vacancies.php"); exit;
}
if (isset($_GET["delete"])) {
  $pdo->prepare("DELETE FROM vacancies WHERE id=?")->execute([$_GET["delete"]]);
  header("Location: vacancies.php"); exit;
}
$vacancies=$pdo->query("SELECT * FROM vacancies ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="UTF-8"><title>Вакансии</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>Модуль "Вакансии"</header>
<nav>
  <a href="home.php">Главная</a>
  <a href="seekers.php">Соискатели</a>
  <a href="resumes.php">Резюме</a>
  <a href="vacancies.php">Вакансии</a>
  <a href="applications.php">Отклики</a>
  <a href="placements.php">Трудоустройства</a>
  <a href="logout.php">Выход</a>
</nav>
<div class="container">
  <h2>Добавить вакансию</h2>
  <form method="post">
    <input type="hidden" name="add" value="1">
    Должность:<input type="text" name="title" required>
    Описание:<textarea name="description"></textarea>
    Зарплата от:<input type="number" name="salary_from">
    Зарплата до:<input type="number" name="salary_to">
    <input type="submit" value="Добавить">
  </form>
  <h2>Список вакансий</h2>
  <table>
    <tr><th>ID</th><th>Должность</th><th>Описание</th><th>Зарплата от</th><th>Зарплата до</th><th>Статус</th><th>Действие</th></tr>
    <?php foreach($vacancies as $v): ?>
    <tr>
      <td><?=$v["id"]?></td>
      <td><?=htmlspecialchars($v["title"])?></td>
      <td><?=nl2br(htmlspecialchars($v["description"]))?></td>
      <td><?=$v["salary_from"]?></td>
      <td><?=$v["salary_to"]?></td>
      <td><?=$v["status"]?></td>
      <td><a class="delete-btn" href="vacancies.php?delete=<?=$v["id"]?>" onclick="return confirm('Удалить вакансию?')">Удалить</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>
