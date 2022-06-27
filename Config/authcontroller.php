<?php
require 'db.php';
require_once 'emailcontroller.php';

global $conn, $errors,$fullname,$email;
session_start();

$fullname = "";
$email = "";
$password = "";
$gender = "";
$country = "";
$favcrypto = "";
$progress = "";
$plans = "";
$userwallet = "";
$balance ="";


$errors = [];

//if user click on the sign up button
if (isset($_POST['signup-btn'])) {
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$passwordconf = $_POST['passwordconf'];
$gender = $_POST['gender'];
$country =$_POST['country'];


    if (empty($_POST['fullname'])) {
        $errors['fullname'] = 'Full Name required';
    }
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email required';
    }
    if (empty($_POST['password'])) {
        $errors['password'] = 'Password required';
    }
    if (isset($_POST['password']) && $_POST['password'] !== $_POST['passwordconf']) {
        $errors['passwordConf'] = 'The two passwords do not match';
    }
//sign user up

    $token = bin2hex(random_bytes(50)); // generate unique token
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); //encrypt password
    $verified = false;
    $favcrpyto = 'n/a' ;
    $userwallet = 'n/a' ;
    $plans = 0 ;
    $progress = 1 ;
    $balance = 0;

    $emailquery = "SELECT * FROM users WHERE  email=? LIMIT 1";
    $stmt =$conn->prepare($emailquery);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result= $stmt ->get_result();
    $usercount = $result->num_rows;

    if ($usercount > 0){
        $errors["email"] = "Email Already Exists!";
    }
}
if (count($errors) === 0) {
    $query = "INSERT INTO users SET fullname=?, email=?, token=?, password=? ,gender=?, country=?, favcrypto=? ,userwallet=? ,plans=?, progress=? ,balance=?" ;
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssssssiid', $fullname, $email, $token, $password , $gender, $country , $favcrpyto, $userwallet, $plans, $progress,$balance );
    $result = $stmt->execute();
    if ($result) {
        $user_id = $stmt->insert_id;
        $stmt->close();

        // TO DO: send verification email to user
        // sendVerificationEmail($email, $token);

        $_SESSION['id'] = $user_id;
        $_SESSION['fullname'] = $fullname;
        $_SESSION['country'] = $country;
        $_SESSION['gender'] = $gender;
        $_SESSION['email'] = $email;
        $_SESSION['favcrypto'] = $favcrpyto;
        $_SESSION['userwallet'] = $userwallet;
        $_SESSION['plans'] = $plans;
        $_SESSION['balance'] = $balance;
        $_SESSION['progress'] = $progress;
        $_SESSION['verified'] = $verified;
        sendVerificationEmail($email, $token);

        $_SESSION['message'] = 'You are logged in!';
        $_SESSION['type'] = 'alert-success';
        header('location: verify-email.php');
    } else {
        $_SESSION['error_msg'] = "Database error: Could not register user";
    }
}


//Login system


if (isset($_POST['login-btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($_POST['email'])) {
        $errors['email'] = 'Email required';
    }
    if (empty($_POST['password'])) {
        $errors['password'] = 'Password required';
    }
    if (count($errors) === 0){

        $query = "SELECT * FROM users WHERE email=? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt ->bind_param('s', $email );
        $stmt -> execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])){
            //successful login

            $_SESSION['id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['country'] = $user['country'];
            $_SESSION['favcrypto'] =  $user['favcrypto'];
            $_SESSION['userwallet'] = $user['userwallet'];
            $_SESSION['plans'] =  $user['plans'];
            $_SESSION['progress'] = $user['progress'];
            $_SESSION['balance'] = $user['balance'];
            $_SESSION['gender'] = $user['gender'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['verified'] = $user['verified'];

            $_SESSION['message'] = 'You are logged in!';
            $_SESSION['type'] = 'alert-success';
            if (! $_SESSION['verified']){
                header('location: verify-email.php');
            }else{
                header('location: dashboard.php');
            }



        } else{
            $errors['login_fail'] = "Wrong Credentials";

        }
    }
}


//logout
if(isset($_GET['logout'])){
    session_destroy();
    unset($_SESSION['id']);
    unset($_SESSION['fullname']);
    unset($_SESSION['email']);
    unset($_SESSION['gender']);
    unset($_SESSION['country']);
    unset($_SESSION['verified']);
    header('location: login.php');
    exit();


}



//Profile Edit

if (isset($_POST['edit-btn'])) {

        $id = $_SESSION['id'];
        $email = $_SESSION['email'];
        $fname = $_POST['fname'];
        $newwallet = $_POST['wallet'];
        $newcrypto = $_POST['crypto'];

        $query = "SELECT * FROM users WHERE id=? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user['id'] === $_SESSION['id']) {
            $query = "UPDATE users SET fullname=?,favcrypto=?,userwallet=? WHERE id= '$id' ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sss', $fname, $newcrypto, $newwallet);
            $result = $stmt->execute();

            $_SESSION['fullname'] = $fname;
            $_SESSION['favcrypto'] = $newcrypto;
            $_SESSION['userwallet'] =  $newwallet;

            header('location: profile.php?update=1');

        } else {
            header('location: profile.php?update=0');
        }


    }


//plans

if(isset($_GET['goldplan'])){
    if (! $_SESSION['balance']>= 100000 ){
        header('location: plans.php?error=1');

    }
   else{
       $id = $_SESSION['id'];

       $query = "UPDATE users SET plans='4',balance = balance -100000  WHERE id=' $id'";
       if ($conn->query($query) === TRUE) {
           $query = "SELECT * FROM users WHERE id=? LIMIT 1";
           $stmt = $conn->prepare($query);
           $stmt->bind_param('i', $id);
           $stmt->execute();
           $result = $stmt->get_result();
           $user = $result->fetch_assoc();

           if ($user['id'] === $_SESSION['id']) {
               //bind new db values after plan purchase
               $_SESSION['balance']= $user['balance'];
               $_SESSION['plans']= $user['plans'];

           }


           header('location: plans.php?success=1');
       } else {
           echo "Error updating record: " . $conn->error;
       }

       $conn->close();

   }

}if(isset($_GET['silverplan'])){
    if (! $_SESSION['balance']>= 50000 ){
        header('location: plans.php?error=1');

    }
    else{
        $id = $_SESSION['id'];

        $query = "UPDATE users SET plans='3',balance= balance -50000 WHERE id=' $id'";
        if ($conn->query($query) === TRUE) {
            $query = "SELECT * FROM users WHERE id=? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user['id'] === $_SESSION['id']) {
                //bind new db values after plan purchase
                $_SESSION['balance']= $user['balance'];
                $_SESSION['plans']= $user['plans'];

            }



                header('location: plans.php?success=1');
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $conn->close();

    }

}



// resend mail

if(isset($_GET['resend'])){
    $email = $_SESSION['email'];
    global $token;


    sendVerificationEmail($email, $token);

    header('location: verify-email.php?resent=1');
}