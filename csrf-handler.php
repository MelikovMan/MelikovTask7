<?php
   include('lib.php');
   configSessionCookie();
   session_start();   
   if (isset($_POST['param']) && isset($_SESSION["login"]))
   	{
   	if ($_POST['param'] === $_SESSION["login"]){
   		echo $_SESSION['csrf_token'];

   	} 
    else echo "null";
   	}  
?>