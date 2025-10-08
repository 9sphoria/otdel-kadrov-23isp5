<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: index.php"); exit; }

$dsn="mysql:host=localhost;dbname=hr_agency;charset=utf8mb4";
$pdo=new PDO($dsn,"root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["add"])) {
  $vacancy_id=(int)$_POST["vacancy_id"];
  $seeker_id=(int)$_POST["seeker_id"];
  $resume=$pdo->prepare("SELECT id FROM resumes WHERE seeker_id=? LIMIT 1");
  $resume->execute([$seeker_id]);
  $resume_id=$resume->fetchColumn();
  if ($resume_id) {
    $stmt=$pdo->prepare("INSERT INTO applications(vacancy_id,resume_id) VALUES (?,?)");
    $stmt->execute([$vacancy_id,$resume_id]);
    header("Location: applications.php"); exit;
  } else {
    $error="⚠ У выбранного соискателя нет резюме!";
  }
}

$seekers=$pdo->query("SELECT * FROM seekers ORDER BY fio")->fetchAll(PDO::FETCH_ASSOC);
$vacancies=$pdo->query("SELECT * FROM vacancies WHERE status<>'закрыта' ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$applications=$pdo->query("
  SELECT a.id,s.fio,v.title,a.created_at,a.status
  FROM applications a
  JOIN resumes r ON a.resume_id=r.id
  JOIN seekers s ON r.seeker_id=s.id
  JOIN vacancies v ON a.vacancy_id=v.id
  ORDER BY a.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="UTF-8"><title>Отклики</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>Модуль "Отклики"</header>
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
  <h2>Создать отклик</h2>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post">
    <input type="hidden" name="add" value="1">
    Соискатель:<select name="seeker_id" required>
      <option value="">-- выберите --</option>
      <?php foreach($seekers as $s): ?><option value="<?=$s["id"]?>"><?=htmlspecialchars($s["fio"])?></option><?php endforeach; ?>
    </select>
    Вакансия:<select name="vacancy_id" required>
      <option value="">-- выберите --</option>
      <?php foreach($vacancies as $v): ?><option value="<?=$v["id"]?>"><?=htmlspecialchars($v["title"])?></option><?php endforeach; ?>
    </select>
    <input type="submit" value="Создать отклик">
  </form>
  <h2>Список откликов</h2>
  <table>
    <tr><th>ID</th><th>Соискатель</th><th>Вакансия</th><th>Дата</th><th>Статус</th></tr>
    <?php foreach($applications as $a): ?>
    <tr>
      <td><?=$a["id"]?></td>
      <td><?=htmlspecialchars($a["fio"])?></td>
      <td><?=htmlspecialchars($a["title"])?></td>
      <td><?=$a["created_at"]?></td>
      <td><?=$a["status"]?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>
