<?php 
function connectToDB($user,$pass){
    $db = new PDO('mysql:host=localhost;dbname=u47551', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    return $db;
}
function connectDB(){
    $user = 'u47551';
    $pass = '4166807';
    $db = new PDO('mysql:host=localhost;dbname=u47551', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    return $db;
}
?>