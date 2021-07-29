<?php
session_start();
$message = "";
$authenticated = false;
$blocked = true;
$cookie_name = "SomeSortOfAuthCookieName";
$cookie_value = "AReallyLongRandomCookieValueThatWouldBeVeryHardToForgeOrAtLeastAsHardAsForgingABasicAuthHeader";
$cookie_domain = ".domain.com";
$user = '2100';
$pass = '1000';

$db = new SQLite3('blacklist.db');
$db->exec("CREATE TABLE IF NOT EXISTS t1(ip TEXT PRIMARY KEY, isotime TEXT)");


$res = $db->query("SELECT * FROM t1 WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
$row = $res->fetchArray(1);
if($row != false){
	$pastdate = new DateTime($row['isotime']);
	$now = new DateTime("now");
	$hours = $pastdate->diff($now)->h;
	if($hours > 24){
		$blocked = false;
	}
}else {
	$blocked = false;
}


if(isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $cookie_value) {
  $authenticated = true;
}
if(isset($_POST["password"])) {	
	/* Clear ban if correct area has been clicked */
	if($_POST["username"] == '0' && $_POST["password"] == '0'){
	   $db->query(	"DELETE FROM t1 WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
	   $message = "<h2>Ban lifted</h2>";
	   $blocked = false;
	}
	else{
		$authenticated = $_POST["username"] == $user && $_POST["password"] == $pass && $blocked == false;
		if(!$authenticated) {
			$message = "<h2>Input incorrect: 24 uur ban on your IP-adres</h2>";
			$db->query(	"INSERT INTO t1(ip, isotime)" . 
					"VALUES ('".$_SERVER['REMOTE_ADDR']."', datetime('now', 'localtime'))" . 
					"ON CONFLICT(ip) DO UPDATE SET isotime = datetime('now', 'localtime')");
		}
		else {
			setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/", $cookie_domain);
			header("Location: https://" . $_POST["return"]);
		}
	}
}


if($authenticated) {
  header("X-CustomAuth: authenticated", true, 200);
} else {
  header("X-CustomAuth: unauthenticated", true, 401);
}	


if (isset($_GET['auth'])) { 

if($blocked){
	$message = "<h2>You need to wait another ". (24-$hours) ." hours before your next login attemt</h2>";
}

echo $message;

?>
<!DOCTYPE html>
<html>
   <head>
     <title>Authentication Required</title>
     <meta http-equiv='content-type' content='text/html;charset=utf-8' />
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="stylesheet" href="/css/bootstrap.min.css">
	 <link rel="stylesheet" href="/css/custom.css">
	 <script>
		window.addEventListener("DOMContentLoaded", function () {
		  var form = document.getElementById("form-id");
		  var elements = document.getElementsByClassName("box");

		  Array.from(elements).forEach(function(element) {
			element.addEventListener("click", function () {
				var targetElement = event.target || event.srcElement;
				document.getElementById("username").value = element.getAttribute("x");
				document.getElementById("password").value = element.getAttribute("y");
                                console.log(element);
                                form.submit();
			});
		  });
		});
	 </script> 
  </head>
<body>
  <div class="container">
    <h3 class="text-center">Klik op Andringa's hoofd om in te loggen</h3>
	<div class="row">
       <div class="col-md-12">
	 <figure id="projectsvg" class='img-fluid w-100'>
	   <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 4096 3072" preserveAspectRatio="xMinYMin meet" >
	     <image width="4096" height="3072" xlink:href="/static/SAM_2050.JPG"></image
	     <g class="hover_group" opacity="0">
                <?php
                   for($i=0; $i <4100; $i=$i+100){
                     for($j=0; $j < 3100; $j=$j+100){
		      echo '<rect class="box" x="' .$i. '" y="' .$j. '" opacity="0.0" fill="#FFFFFF" width="100" height="100"></rect>';
                     }
                   }
                ?>
	     </g>
	   </svg>
	 </figure>
       </div>
    </div>

    <form id="form-id" name="input" action="" method="post">
      <div class="form-group">
        <label for="username">Username:</label><input type="text" value="" id="username" name="username" class="form-control"/>
      </div>
      <div class="form-group">
        <label for="password">Password:</label><input type="password" value="" id="password" name="password" class="form-control"/>
      </div>
     <input type="hidden" id="return" name="return" value=<?php echo $_GET['return'] ?>> 
      <div class="error"><?= $error ?></div>
      <button type="submit" name="submitbtn" class="btn btn-default">Login</button>
    </form>
  </div>
</body>
</html>
<?php } ?>
