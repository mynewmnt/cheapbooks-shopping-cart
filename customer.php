<html>
<head><title>Message Board</title>
<meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

try {
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbook","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  //print_r($dbh);
  $dbh->beginTransaction();
  
  $dbh->exec('delete from customers where username="smith"');
  $dbh->exec('insert into customers values("smith","'. md5("mypass") . '","405 Austin St, Arlington, TX","705-666","smith@cse.uta.edu")')
        or die(print_r($dbh->errorInfo(), true));
  $dbh->commit();

  $nameErr = $pwdErr ="";
/*
  $stmt = $dbh->prepare('select * from customers');
  $stmt->execute();
  print "<pre>";
  while ($row = $stmt->fetch()) {
    print_r($row);
  }
  print "</pre>";
  */
?>
</br></br>
<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-6 jumbotron">
  <h1>Cheapbooks</h1>
  <p>Assignment 4 by <strong>Remesh Sreemoolam Venkitachalam</strong> </br>in CSE 5334</p>
  </div>

  <?php

session_start();
if(isset($_POST['submit']))
{
 $name=$_POST['uname'];
 $pwd=$_POST['pwd'];

 if($name!=''&&$pwd!='')
 {
   $dbh->beginTransaction();
   $query=$dbh->prepare("select * from customers where username='".$name."' and password='".md5($pwd)."'");
   $query->execute();

   if($row = $query->fetch())
   {
    $_SESSION['uname']=$row['username'];
    $_SESSION['utype']=1;
    header('location:dashboard.php');
   }
   else
   {
    echo "The username or password is incorrect";
   }
 }
 else if($name==''&&$pwd!='')
 {
  $nameErr = "* Name is required";
 }else if($name!=''&&$pwd==''){
  $pwdErr = "* Password is required";
  
 }else{
  $nameErr = "* Name is required";
  $pwdErr = "* Password is required";
 }
}

} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
?>

  <div class="col-sm-4">
  <form name="login" method="post">
    <fieldset >
                    <legend>Sign In</legend>
<table align="center" style="background-color:#EAEAEA">
  <tr><td><label>Username</label></td>
 <td><input type="text" name="uname" width="100%"/></td></tr>
 <tr><td colspan="2"><span class="error" style="color:red;"> <?php echo $nameErr;?></span></td></tr>
<tr><td><label>Password</label></td>
<td><input type="password" name="pwd"/></td></tr>
<tr><td colspan="2"><span class="error" style="color:red;"> <?php echo $pwdErr;?></span></td></tr>
<tr><td></td><td><input type="submit" name="submit" value="Submit"/></td></tr>
<tr><td colspan="2"></br></td></tr>
<tr><td><label>New users </label></td>
<td><a href="register.php"> Register here</a></td></tr>
</table>
</br>
<div align="center" style="color:red;">

</div>
</fieldset>
</form>
</div>
  <div class="col-sm-1"></div>
 </div> 

</body>
</html>
