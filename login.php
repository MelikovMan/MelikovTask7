<?php
include('lib.php');
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
configSessionCookie();
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  // Делаем перенаправление на форму.
  setcookie(session_name(),'',1000000);
  session_destroy();
  setcookie('isLogged','',1000000);
  header('Location: ./');
}


// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $badloging = false;
  $invlog = false;
  $invpass = false;
  if(!empty($_COOKIE['bad_login'])){
      setcookie('bad_login','',1000000);
      $badloging = true;
  }
  if(!empty($_COOKIE['invalid_login'])){
    setcookie('invalid_login','',1000000);
    $invlog = true;
  }
  if(!empty($_COOKIE['invalid_pass'])){
    setcookie('invalid_pass','',1000000);
    $invpass = true;
}
/*$db = connectDB();
$logins = dbQuery($db,"SELECT * FROM login WHERE p_id=:id",array('id'=>22));
$ar = $logins->fetch(PDO::FETCH_ASSOC);
var_dump($ar);
*/
?>
<html lang="en">
<head>
  <meta charset='utf-8'/>
  <link rel="stylesheet" href="style.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'/>
</head>
<body>
  <form action="" method="post">
    Login:
    <input name="login" <?php if($invlog) print('class="error"') ?> /> 
    <?php if($invlog) print('<div> Must only contain letters, numbers and dots </div>') ?>
    <br/>
    Password:
    <input name="pass" <?php if($invpass) print('class="error"') ?>/>
    <?php if($invpass) print('<div> Must only contain letters, numbers and dots </div>') ?>
    <input type="submit" value="Войти" />
  </form>
  <?php if($badloging) print('<div> Invalid login! </div') ?>
</body>
<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
    $db = connectDB($user,$pass);
    $regex = "/^[\w\d\.]+$/";
    if(!preg_match($regex,$_POST['login'])){
      setcookie('invalid_login',1,$cookie_options);
      header('Location: ./login.php');
    }
    if(!preg_match($regex,$_POST['pass'])){
      setcookie('invalid_pass',1,$cookie_options);
      header('Location: ./login.php');
    }
			$stmt = dbQuery($db,"SELECT pass_hash, p_id FROM login WHERE login=:this_login",array('this_login'=>$_POST['login']));
			/*$stmt->bindParam(':this_login',$_POST['login']);
			if($stmt->execute()==false) {
				print_r($stmt->errorCode());
				print_r($stmt->errorInfo());
				exit();
			}
    }
      catch(PDOException $e){
      print('Error : ' . $e->getMessage());
        exit();
    }
    */
	$dbread=array();
  try{
	  $dbread=$stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}
  
    if(empty($dbread['pass_hash']) || !password_verify($_POST['pass'],$dbread['pass_hash'])){
        setcookie('bad_login',1,$cookie_options);
        header('Location: ./login.php');   

    }
    else{
  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.

  // Если все ок, то авторизуем пользователя.
  $_SESSION['login'] = $_POST['login'];
  // Записываем ID пользователя.
  $_SESSION['uid'] = $dbread['p_id'];
  // Делаем перенаправление.
  generateToken();
  header('Location: ./');
    }
}