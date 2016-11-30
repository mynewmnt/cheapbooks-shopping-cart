<?php

session_start();

$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbook","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $dbh->beginTransaction();
  
	  
if($_GET['type']==2){
	$bookno=$_GET['isbn'];
	$stmt = $dbh->prepare("select count(*) as cnt from contains where basketId = " . $_SESSION['basket'] . " and ISBN =". $bookno .";");
    $stmt->execute();
   if($row=$stmt->fetch())
   {
     if($row['cnt']>0)
         $dbh->exec("update contains set number=number+1 where basketId=" . $_SESSION['basket'] . " and ISBN =". $bookno .";")
         or die(print_r($dbh->errorInfo(), true));
     else
         $dbh->exec('insert into contains values('. $bookno. ', '. $_SESSION['basket'] .', 1)')
        or die(print_r($dbh->errorInfo(), true));
   }

   $dbh->exec("update stocks set number=number-1 where warehouseCode = (SELECT * from (SELECT max(warehouseCode) from stocks where 
   	           ISBN=". $bookno." and number>0) as s) and ISBN=". $bookno.";")
         or die(print_r($dbh->errorInfo(), true));

}else if($_GET['type']==3){
	$bookno=$_GET['isbn'];
echo $bookno;
	$stmt = $dbh->prepare("select number as cnt from contains where basketId = " . $_SESSION['basket'] . " and ISBN =". $bookno .";");
    $stmt->execute();
   if($row=$stmt->fetch())
   {

     if($row['cnt']>0)
         $dbh->exec("update contains SET number=number-1 where basketId=" . $_SESSION['basket'] . " and ISBN=" . $bookno . ";")
         or die(print_r($dbh->errorInfo(), true));

         if($row['cnt']==1)
         	$dbh->exec("DELETE from contains where basketId=" . $_SESSION['basket'] . " and number=0;")
         or die(print_r($dbh->errorInfo(), true));
   
   }
     		
    $dbh->exec("UPDATE stocks set number=number+1 where warehouseCode = (SELECT * from (SELECT max(warehouseCode) from stocks where 
   	           ISBN=". $bookno.") as s) and ISBN=". $bookno.";")
         or die(print_r($dbh->errorInfo(), true));


}else /*if($_GET['type']==4){

   	$stmt = $dbh->prepare("select contains.ISBN, contains.number, shoppingbasket.username, max(stocks.warehouseCode) as war 
		                   from contains, shoppingbasket, stocks 
                           where contains.basketId=" . $_SESSION['basket'] . " and shoppingbasket.basketId=" . $_SESSION['basket'] . "
                           and stocks.ISBN = contains.ISBN Group By contains.ISBN;");
    $stmt->execute();
   if($row=$stmt->fetch())
   {

   	 $dbh->exec("insert into shoppingorder values(". $row[war].",". $row[ISBN] .",'". $row[username] ."'," . $row[number] .");")
         or die(print_r($dbh->errorInfo(), true));

     $dbh->exec("DELETE from contains where basketId=" . $_SESSION['basket'] . ";")
         or die(print_r($dbh->errorInfo(), true));

   }
}else*/
	$bookno=1;

$dbh->commit();


//echo $bookno;

?>
<html>
<head><title>Shop Cart</title>
  <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
</br>
<div align="center" style="color:black; background-color: #D8D8D8; border:1px solid;"><h1>CheapBooks</h1></div></br>
<div class="container">
	<div class="row col-sm-1"></div>
<div class="row col-sm-6">
	<?php 

echo "Welcome, <h3 style=\"display:inline;\"><strong>" . $_SESSION['uname'] . "!</strong></h3>";
$_SESSION['utype']=2;
?></div>
<div class="row col-sm-3" align="right"><a href="dashboard.php"><input type="button" value="Continue Shopping"/></a>  Items:
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
 }catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}

?></div>
<div class="row col-sm-2" align="right"><a href="logout.php"><input type="button" value="Logout"/></a></div>
</div>

<?php 
try {
  $stmt = $dbh->prepare("select sum(number) as cart from contains
                        where basketId = (select max(basketId) from shoppingbasket where username= '". $_SESSION['uname'] . "');");
  $stmt->execute();
  if($row=$stmt->fetch())
   { if($row['cart']!=NULL){

?>
<div>
	<div class="col-sm-1"></div>
	<div><h3>Your Shopping Cart</h3></div></div>

<div class="container">
	<div class="col-sm-1"></div>
	<div class="col-sm-6 well">

<?php

$total=0;

$stmt = $dbh->prepare("select book.Title, book.price, book.ISBN, contains.number as qty from contains, book
                        where book.ISBN IN (SELECT ISBN from contains where basketId='" . $_SESSION['basket'] . "')
                        and contains.basketId='". $_SESSION['basket'] . "'and contains.ISBN=book.ISBN GROUP BY book.ISBN;");

$stmt->execute();
 while ($row = $stmt->fetch()) {

 $total=$total+($row['price']*$row['qty']);
?>
		<div class="container">
			<div class="col-sm-4">
    <h4 style="color:red;"><?php echo $row['Title'] ?></h4> ISBN : <?php echo $row['ISBN'] ?></br>Quantity : <?php echo $row['qty'] ?></div>
    <div class="col-sm-2"><h4 style="color:red;"> $ <?php echo $row['price'] ?> </h4></br>
    <a href="basket.php?isbn=<?php echo $row['ISBN'] ?>&amp;type=3"><input type="button" value="Delete"></input></a></div>
</div></br>
 

 <?php
}
 ?>
 </div>
 <div class="col-sm-1"></div>
 <div class="col-sm-3 well" align="center">
 	<h5>Finished Shopping?</h5>
 	<h4>Total Price : <b style="color:red;">$ <?php echo $total;?></b></h4>
 <a href="cart.php"><input type="button" class="btn btn-info btn-lg" value="Buy" style="width:50%;"/></a>
</div>
 <div class="col-sm-1"></div>
 </div>

 <?php
}
 else{
 	?>
 	<div>
	<div class="col-sm-1"></div>
	<div class="col-sm-5"><div><h3>Your Shopping Cart has no items</h3></div>
		<div><h4>Go back to <a href="dashboard.php"> Search to Continue Shopping</a></h4></div></div>
	</div>
<?php }
     
   }
 }catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
 ?>

</body>
</html>