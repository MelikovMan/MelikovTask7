<?php
include('lib.php');
configSessionCookie();
function echoUserInfo($row,$db){
$superstate = dbQuery($db,"SELECT name FROM superpowers WHERE person_id=:id",array('id'=>$row['id']));
print(
"<table border='1'> 
<caption> Id:
".$row['id'].
"
</caption>
<tr>
  <th>name</th>
  <th>email</th>
  <th>birthdate</th>
  <th>sex</th>
  <th>limb_count</th>
  <th>bio</th>
</tr>
  <td>".strip_tags($row['name'])."</td>
  <td>".strip_tags($row['email'])."</td>
  <td>".strip_tags($row['birthdate'])."</td>
  <td>".strip_tags($row['sex'])."</td>
  <td>".strip_tags($row['limb_count'])."</td>
  <td>".strip_tags($row['bio'])."</td>
<tr>
</table> 
".
"
<table border='1'>
  <caption>Superpowers</caption>
  <tr>");
while($super = $superstate->fetch(PDO::FETCH_ASSOC)){
  print("
    <th>".strip_tags($super['name'])."</th>
  ");
}
print("
  <tr/>
<table/>
");
print("
<form action='./admin.php' method='POST'>
<label>
  <input type='hidden' name='id' value='".$row['id']."'/>
</label>
<input type='submit' name='action' value='modify'/>
<input type='submit' name='action' value='delete'/>
</form>
"
);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(empty($_POST['id']) || empty($_POST['action'])){
    print('Empty data sent!');
    exit();
  }
  if(!is_numeric($_POST['id'])){
    print("Id must be a number!");
    exit();
  }
  if($_POST['action']!='modify' && $_POST['action']!='delete'){
    print("Admin method not supported");
  }
  $id = intval($_POST['id']);
  print_r($id);
  $action = $_POST['action'];
  $login = null;
  if($action == 'modify'){
   try {
      $db = connectDb();
      $getLogin = dbQuery($db, "SELECT login FROM login WHERE p_id=:id",array('id'=>$id));
      $login = $getLogin->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
    }
    if(empty($login)){
      print("login couldn't be found, check id validity");
      exit();
    }
    session_start();
    $_SESSION['login'] = $login;
    $_SESSION['uid'] = $id;
    generateToken();
    header('Location: ./');
  }
  if($action == 'delete'){
    try {
      $db = connectDb();
      $rmLogin = dbQuery($db,"DELETE FROM login WHERE p_id=:id",array('id'=>$id));
      $rmSupers = dbQuery($db,"DELETE FROM superpowers WHERE person_id=:id",array('id'=>$id));
      $rmUsers = dbQuery($db,"DELETE FROM contracts WHERE id=:id",array('id'=>$id));
    header('Location: ./admin.php');
  } catch(PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}
}
}
/*
 * Задача 6. Реализовать вход администратора с использованием
 * HTTP-авторизации для просмотра и удаления результатов.
 **/
#
// Пример HTTP-аутентификации.
// PHP хранит логин и пароль в суперглобальном массиве $_SERVER.
// Подробнее см. стр. 26 и 99 в учебном пособии Веб-программирование и веб-сервисы.
$db = connectDb();
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW'])) {
      $getPWD = dbQuery($db, "SELECT pass_hash FROM admin_auth WHERE login=:login",array(
        'login'=>$_SERVER['PHP_AUTH_USER']
      ));
      $pwdread =  $getPWD->fetch(PDO::FETCH_ASSOC);
      if(empty($pwdread) || !password_verify($_SERVER['PHP_AUTH_PW'],$pwdread['pass_hash']))
      {
        header('HTTP/1.1 401 Unanthorized');
        header('WWW-Authenticate: Basic realm="http://u47551.kubsu-dev.ru/MelikovTask6/admin.php"');
        print('<h1>401 Требуется авторизация</h1>');
        exit();
      }
  }
  if (!empty($_COOKIE[session_name()]) &&
session_start() && !empty($_SESSION['login']) && !empty($_SESSION['uid'])){
  setcookie(session_name(),'',1000000);
  setcookie('isLogged','',1000000);
  session_destroy();
}
  $supercounter = array();
  $allusers = null;
  try {
    $supcount = dbQuery($db,"SELECT name, COUNT(name) as sup_qty FROM superpowers GROUP BY name",null);
    $supercounter=$supcount->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
    $allusers =  dbQuery($db,"SELECT * FROM contracts",null);
  } catch(PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
  }
  ?>
<html lang="en">
<head>
  <meta charset='utf-8'/>
  <link rel="stylesheet" href="style.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'/>
</head>
<body>
 <h1> ADMIN PANEL </h1>
 <div>
    <h2>Superpower Listing</h2>
    <table border="1">
      <caption>Superpower counting</caption>
      <tr>
        <th>Immortality</th>
        <th>Walking Through Walls</th>
        <th>Leviation</th>
      </tr>
      <tr>
        <td><?php if(!empty($supercounter['immortality'])) print(intval($supercounter['immortality'][0])); else print("0");?></td>
        <td><?php if(!empty($supercounter['walkthroughwalls'])) print(intval($supercounter['walkthroughwalls'][0])); else print("0");?></td>
        <td><?php if(!empty($supercounter['levitation'])) print(intval($supercounter['levitation'][0])); else print("0");?></td>
      </tr>
    </table>
 </div>
 <div>
  <h2>User List</h2>
  <?php
  while($row = $allusers->fetch(PDO::FETCH_ASSOC)){
    echoUserInfo($row,$db);
  }
  ?>
 </div> 
</body>
</html>

