<?php

session_start();
	  
$searchterm=$_GET['term'];
$searchtype=$_GET['type'];
//echo $searchterm;

if ($searchterm==NULL)
{
 echo "<p style=\"color:red;\">Please Enter an input search term</p>";
 exit;
}  


 error_reporting(E_ALL);
ini_set('display_errors','On');

try {
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbook","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $dbh->beginTransaction();

  if($searchtype==1){
 $stmt = $dbh->prepare("select book.Title, book.price, stocks.ISBN, sum(stocks.number) as total from stocks, book
                        where stocks.ISBN IN (SELECT ISBN from book where Title like '%$searchterm%')
                        and stocks.ISBN=book.ISBN GROUP BY stocks.ISBN;");
 //echo "Type 1";
}else if($searchtype==2){
 $stmt = $dbh->prepare("select book.Title, book.price, stocks.ISBN, sum(stocks.number) as total from stocks, book
                        where stocks.ISBN IN (SELECT ISBN from writtenby where
                        ssn IN (SELECT ssn from author where Name like '%$searchterm%'))
                        and stocks.ISBN=book.ISBN GROUP BY stocks.ISBN;");
 //echo "Type2";
}

$stmt->execute();
 $display_string="<h3>Results found </h3></br>";

    // output data of each row
 if($stmt->rowCount() > 0){
     while ($row = $stmt->fetch()) {

        $arr[]=$row;
    
        if($row['total']>0){

     	$display_string .= "<div class=\"row well\"><div class=\"col-sm-1\"></div>"; 
     	$display_string .= "<div class=\"col-sm-7\">"; 
        $display_string .= "<h4 style=\"color:red;\">$row[Title]</h4> ISBN : $row[ISBN] </br>Stock available : $row[total]";
        $display_string .= "</div><div class=\"col-sm-3\"><h4 style=\"color:red;\"> $ $row[price] </h4></br>";
        $display_string .= "<a href=\"basket.php?isbn=$row[ISBN]&type=2\"><input type=\"button\" value=\"Add to Basket\"></input></a></div>";	
      	$display_string .="<div class=\"col-sm-1\"></div></div>";
        }

     	}
         $dbh->commit();

         $_SESSION['query'] = $arr;
         $_SESSION['utype']=1;
     }else{
        $_SESSION['utype']=1;
        $display_string .="<h4>None</h4>";

     }

    	echo $display_string;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
	?>
