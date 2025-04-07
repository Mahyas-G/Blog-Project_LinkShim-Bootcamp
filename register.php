<?php
session_start();
if(isset($_SESSION['user'])){
    header("location:dashboard.php ");
    exit;
}

$users = [];
if(file_exists("data/users.json")){
    $users = json_decode(file_get_contents("data/users.json") , true);
}

// $errors = [];
// if($_SERVER['REQUEST_METHOD'] === 'POST'){
//     $username = trim($_POST['username']);
//     $password = trim($_POST['password']);

//     if(&username === '' || $password === ''){
//         $errors = "username & password is required ";
//     }
//     foreach($users as$user){
//         if($user['username'] === $username){
//             $errors []= "username already taken";
//             break; 
//         }
//     }
//     if (empty($errors)){
//         $newuser = 
//     }
//     }
// }
?>

