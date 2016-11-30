<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">

    <script language="javascript" type="text/javascript">

function regFunction(){
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
         var ajaxDisplay = document.getElementById("success");
         ajaxDisplay.innerHTML = ajaxRequest.responseText;
      }
   }

   var usrn=document.getElementById('user').value;
   var pass=document.getElementById('pass').value;
   var email=document.getElementById('email').value;
   var addr=document.getElementById('addr').value;
   var phn=document.getElementById('phn').value;




   /*if (document.getElementById('title').checked) {
        var term = document.getElementById('searchinput').value;
        var type = 1;
    } 
    else if(document.getElementById('author').checked) {
        var term = document.getElementById('searchinput').value;
        var type = 2;
   }
    */

   
   var queryString = "?un=" + usrn + "&pass=" + pass + "&email=" + email + "&addr=" + addr + "&phn=" + phn;

   ajaxRequest.open("GET", "regDB.php" + queryString, true);
   ajaxRequest.send(null); 
}
</script>

</head>
<body>
</br>
<div align="center" style="color:black; background-color: #D8D8D8; border:1px solid;"><h1>Cheapbooks</h1></div></br>
<form method="post">
	<fieldset>
                    <legend>Register</legend>
	<table align="center" width="70%">
		<tr><td><label>Username</label></td><td colspan="2"><input type="text" id="user"/></td></tr>
		<tr><td><label>Password</label></td><td colspan="2"><input type="text" id="pass" required/></td></tr>
		<tr><td><label>Email</label></td><td colspan="2"><input type="text" id="email" required/></td></tr>
		<tr><td><label>Address</label></td><td colspan="2"><input type="textarea" id="addr" required/></td></tr>
		<tr><td><label>Phone Number</label></td><td colspan="2"><input type="text" id="phn" required/></td></tr>
		<tr><td></td><td><input type="button" id="register" onclick="regFunction();" value="Register"/></td></tr>
	</table>
</fieldset>
</form>

<div class="container" id="success"><h4>Please Register</h4>
	</div>
<div class="container"><h4>Click here to <a href="customer.php"><b>LOG IN</b></a></h4></div>

</body>
</html>