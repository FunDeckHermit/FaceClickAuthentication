<?php
session_start();
$error = "";
$authenticated = false;
$cookie_name = "SomeSortOfAuthCookieName";
$cookie_value = "AReallyLongRandomCookieValueThatWouldBeVeryHardToForgeOrAtLeastAsHardAsForgingABasicAuthHeader";
$cookie_domain = ".domain.com";
$user = '2100';
$pass = '1000';


if(isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $cookie_value) {
  $authenticated = true;
}
if(isset($_POST["password"])) {
  $authenticated = $_POST["username"] == $user && $_POST["password"] == $pass;
  if(!$authenticated) {
    $error = "Invalid username or password.";
  }
  else {
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/", $cookie_domain);
    header("Location: https://" . $_POST["return"]);
  }
}
if($authenticated) {
  header("X-CustomAuth: authenticated", true, 200);
} else {
  header("X-CustomAuth: unauthenticated", true, 401);
}
if (isset($_GET['auth'])) { ?>
<!DOCTYPE html>
<html>
   <head>
     <title>Authentication Required</title>
     <meta http-equiv='content-type' content='text/html;charset=utf-8' />
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="stylesheet" href="/css/bootstrap.min.css">
	 <link rel="stylesheet" href="/css/custom.css">
	 <style>
		#form-id
		{
		  display:none;
		}

		.hover_group:hover
		{
			opacity:1;
		}

		#projectsvg
		{
			position: relative;
			width: 100%;
			padding-bottom: 77%;
			vertical-align: middle;
			margin: 0;
			overflow: hidden;
		}

		#projectsvg svg
		{
			display: inline-block;
			position: absolute;
			top: 0;
			left: 0;
		}
	 </style>
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
