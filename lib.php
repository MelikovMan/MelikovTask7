<?php
$cookie_options = array(
    'expires' => time() + 30 * 24 * 60 * 60,
    'samesite' => 'Strict'
); 
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
function generateToken(){
    return $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
?>