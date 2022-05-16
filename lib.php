<?php
$cookie_options = array(
    'expires' => time() + 30 * 24 * 60 * 60,
    'samesite' => 'Strict'
);
function configSessionCookie(){
$cookie_session_options = array(
    'lifetime' => 600,
    'samesite' =>'Strict'
);
session_set_cookie_params($cookie_session_options); 
}
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
function dbQuery($db,$query,$params){
    try{
            $state = $db->prepare($query);
        if(isset($params)){
            if($state->execute($params)==false) {
                print_r($state->errorCode());
                print_r($state->errorInfo());
                exit();
            }
        } else {
            if($state->execute()==false) {
                print_r($state->errorCode());
                print_r($state->errorInfo());
                exit();
            }
        }
            return $state;
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
}
function generateToken(){
    return $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
?>