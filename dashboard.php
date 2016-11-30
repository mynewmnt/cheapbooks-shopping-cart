<!DOCTYPE html>
<html>
<head><title>DashBoard</title>
  <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script language="javascript" type="text/javascript">
function ajaxFunction(){
   var ajaxRequest;  // The variable that makes Ajax possible!
   try{
      // Opera 8.0+, Firefox, Safari
      ajaxRequest = new XMLHttpRequest();
   }catch (e){      
      // Internet Explorer Browsers
      try{
         ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e) {
         
         try{
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
         }catch (e){
         
            // Something went wrong
            alert("Your browser broke!");
            return false;
         }
      }
   }
   
   // Create a function that will receive data
   // sent from the server and will update
   // div section in the same page.
   ajaxRequest.onreadystatechange = function(){
   
      if(ajaxRequest.readyState == 4){
         var ajaxDisplay = document.getElementById("ajaxresult");
         ajaxDisplay.innerHTML = ajaxRequest.responseText;
      }
   }

   if (document.getElementById('title').checked) {
        var term = document.getElementById('searchinput').value;
        var type = 1;
    } 
    else if(document.getElementById('author').checked) {
        var term = document.getElementById('searchinput').value;
        var type = 2;
   }
    

   
   //alert(term);
   var queryString = "?term=" + term + "&type=" + type;

   ajaxRequest.open("GET", "searchDB.php" + queryString, true);
   ajaxRequest.send(null); 
}
</script>
</head>
<body>
</br>
<div align="center" style="color:black; background-color: #D8D8D8; border:1px solid;"><h1>CheapBooks</h1></div></br>
<div class="container">
<div class="row col-sm-7">
	<?php 
session_start(); 
echo "Welcome, <h3 style=\"display:inline;\"><strong>" . $_SESSION['uname'] . "!</strong></h3>";
?></div>
<div class="row col-sm-3" align="right"><a href="basket.php?isbn=1&amp;type=1"><input type="button" value="Shopping Basket"/></a>  Items:
<?php 
try {

  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbook","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $dbh->beginTransaction();

  $stmt = $dbh->prepare("select max(basketId) as bask from shoppingbasket where username= '". $_SESSION['uname'] . "';");
  $stmt->execute();
  if($row=$stmt->fetch())
   { $_SESSION['basket']=$row['bask'];
   }

  $stmt = $dbh->prepare("select sum(number) as cart from contains
                        where basketId = (select max(basketId) from shoppingbasket where username= '". $_SESSION['uname'] . "');");
  $stmt->execute();
  if($row=$stmt->fetch())
   { if($row['cart']!=NULL)
         echo $row['cart'];
     else
         echo "0";
   }
    $dbh->commit();
 }catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}

?></div>
<div class="row col-sm-2" align="right"><a href="logout.php"><input type="button" value="Logout"/></a></div>
</div>
<div class="container">
  <h2>Search for Books</h2>
  <div class="panel panel-default" style="background-color:#E9F7FC; width=300px;">
    <div class="panel-body">
      <form name="searchform" method="post">
        <div class="row col-sm-8">
        <input type="radio" name="search" id="title" checked/><label>Search by Title</label>
        <input type="radio" name="search" id="author"/><label>Search by Author Name</label></br>
        <input type="text" name="name" id="searchinput" style="display:block; width:100%;"/></br>
        <input type="button" name="find" onclick="ajaxFunction();" value="Search"/></br>
        </div>
      </form>
    </div>
  </div>
  </div>

  <div class="container" id="ajaxresult">
  <?php
if($_SESSION['utype']==2){ 

 if(isset($_SESSION['query'])){

  ?><h4>Previous Result</h4>
  <?php

  foreach($_SESSION['query'] as $book){


$stmt = $dbh->prepare("select sum(number) as num from stocks where ISBN=". $book[2] . ";");
        $stmt->execute();
        if($row = $stmt->fetch()){
          $num= $row['num'];
        }

    if($num>0){


?><div class="row well">
    <div class="col-sm-1"></div>
      <div class="col-sm-7">
        <h4 style="color:red;"><?php echo $book[0]; ?></h4> ISBN : <?php echo $book[2]; ?></br>Stock available : <?php echo $num; ?>
        </div>
        <div class="col-sm-3"><h4 style="color:red;"> $<?php echo $book[1];?> </h4></br>
       <a href="basket.php?isbn=<?php echo $book[2]; ?>&amp;type=2"><input type="button" value="Add to Basket"></input></a></div>
       <div class="col-sm-1"></div></div>
<?php
  }
}
}

}
?>
</div>


</body>
</html>