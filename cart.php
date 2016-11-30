<?php
session_start();

$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbook","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $dbh->beginTransaction();

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
<div class="row col-sm-3" align="right"><a href="dashboard.php?type=2"><input type="button" value="Continue Shopping"/></a>  Items: 0</div>

<div class="row col-sm-2" align="right"><a href="logout.php"><input type="button" value="Logout"/></a></div>
</div>

<div>
	<div class="col-sm-1"></div>
	<div><h3>Congrats! You bought the following items</h3>
	</div></div>

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
    </div>
</div></br>
 

 <?php
}
 ?>
</br>
 <h4  style="color:blue; text-align:center; border:1px solid blue; padding:2px;"><strong>Total Price : $<?php echo $total;?> </strong></h4>
 </div>
 <div class="col-sm-1"></div>
 <div class="col-sm-3 well" align="center">
 	<h4>Have more Shopping?</h4>
 <a href="dashboard.php"><input type="button" class="btn btn-info btn-lg" value="Shop" style="width:50%;"/></a>
</div>
 <div class="col-sm-1"></div>
 </div>

 <?php

 $stmt = $dbh->prepare("select contains.ISBN, contains.number, shoppingbasket.username, max(stocks.warehouseCode) as war 
		                   from contains, shoppingbasket, stocks 
                           where contains.basketId=" . $_SESSION['basket'] . " and shoppingbasket.basketId=" . $_SESSION['basket'] . "
                           and stocks.ISBN = contains.ISBN Group By contains.ISBN;");
    $stmt->execute();
   while($row=$stmt->fetch())
   {

   	 $dbh->exec("insert into shoppingorder values(". $row['war'].",". $row['ISBN'] .",'". $row['username'] ."'," . $row['number'] .");")
         or die(print_r($dbh->errorInfo(), true));

     $dbh->exec("DELETE from contains where basketId=" . $_SESSION['basket'] . ";")
         or die(print_r($dbh->errorInfo(), true));

         $dbh->commit();
     }


echo " ";
?>


	</body>
	</html>

