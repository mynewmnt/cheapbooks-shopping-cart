<?php

session_start();
	
$user=$_GET['un'];
$pass=$_GET['pass'];
$email=$_GET['email'];
$addr=$_GET['addr'];
$phone=$_GET['phn'];



if($user==NULL || $pass==NULL || $email==NULL || $addr==NULL || $phone==NULL)
{
 echo "<p style=\"color:red;\">Please Enter all fields</p>";
 exit;
} 

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo "<p style=\"color:red;\">Please Enter valid email</p>";
 exit;
   
}
if (!preg_match("/^[0-9]{10}$/",$phone)) {
  echo "<p style=\"color:red;\">Please Enter valid phone number. No Special characters. Only 10 digits including area code</p>";
 exit;
}


error_reporting(E_ALL);
ini_set('display_errors','On');

try {
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbook","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $dbh->beginTransaction();

  $dbh->exec("insert into customers values('". $user . "','" . md5($pass) ."','" . $addr . "','" . $phone . "','" . $email ."');")
        or die(print_r($dbh->errorInfo(), true));
        
  $dbh->exec("insert into shoppingbasket(username) values('". $user . "');")
        or die(print_r($dbh->errorInfo(), true));
    

    $dbh->commit();
  

  echo "You have successfully Registered!";

}
catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}


?>