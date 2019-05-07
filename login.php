<?php
  require_once "pdo.php";
  require_once "util.php";
  require_once "head.php";

      if(!isset($_SESSION)) 
      { 
          session_start(); 
      } 

    if( isset($_POST['cancel'])){
      header('Location: index.php');
      return;
    }

  $errors = [];
  $success = [];
       
    //BEGIN-user login validation

    if ( isset($_POST['email']) && isset($_POST['pass']) ) {
        
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['pass']);
    
        if ( strlen($email) < 1 || strlen($password) < 1 ) {
            
            $_SESSION['message'] = "<p style = 'color:red'>username and password are required.</p>\n";
            error_log("Username and password are required.", 0);
            header("Location: login.php");
            return;    
        }
        
        $atsign = strpos($email, '@');
        if ($atsign == false) {

            $_SESSION['message'] = "<p style = 'color:red'>Username must have an at-sign(@). Incorrect username or password.</p>\n";
            error_log("Username must have an at-sign (@).", 0);
         // SECRET? error_log("Did you enter the correct email address?");
              header("Location: login.php");
              return;

              } 
        
        if (!$errors) { 

            $salt = 'XyZzy12*_';   // for password php123
            $check = hash('md5', $salt.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id, name FROM users
                WHERE email = :em AND password = :pw');
                $stmt->execute(array( 
                    ':em' => $_POST['email'], 
                    ':pw' => $check));            
                $row = $stmt->fetch(PDO::FETCH_ASSOC);         
    
    //////
            if ( $row !== false ) {

                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['message'] = "<p style = 'color:green'>Login success</ p>\n";
                $_SESSION['success'] = "<p style = 'color:red'>Login failed.</p>\n";
                header("Location: index.php");
                return;

            } else {
                $_SESSION['message'] = "<p style = 'color:red'>Login failed.</p>\n";
                $_SESSION['error'] = "<p style = 'color:red'>Login failed.</p>\n";
                // console.log("Login failed");
                error_log("Login failed", 0);
                header("Location: index.php");
                return;
            }
/////
        }
            
    }
    

// Fall through into the View
?>


<!-- login page -->
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Yumei Leventhal login.php</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- bootstrap files in head.php -->

<link rel="stylesheet" type="text/css" media="screen" href="main.css">


<!-- <script src="main.js"></script> -->
</head>

<body>
<div class="container">
    <h1>Login Page</h1>
    <br>
    <br>
    <h3>
    <label for="login">Please Log In</label></h3>    
    <br>

    <!-- < ?php flashMessages();? > -->
    
    <a href="login.php">Please log in</a>
    <form method="POST">  
        <label for="email">User Name</label>
        <input type="text" name="email" id="email" size="30" 
                value="<?=htmlentities('');?>" > <br>
        <label for="id_1723">Password</label>
        <input type="password" name="pass" id="id_1723" 
                value="<?=htmlentities('');?>">
        <input type="submit" onlick="return doValidate();" value="Log In" >
        <input type="submit" name="cancel" value="Cancel">
     </form>


    <!-- Moved to util.php -->
    <!-- <script>
    function doValidate() {
            console.log('Validating...');
            try {
                addr = document.getElementById('email').value;
                pw = document.getElementById('id_1723').value;
                console.log("Validating addr="+addr+" pw="+pw);
                if ( addr== null || addr =="" || "@" == null || "@" == "" || pw = null || pw == "") {
                    alert("Both fields must be filled out");
                    return false;
                }

                if ( addr.indexOf('@') == -1) {
                    alert("Invalid emaill address");
                    return false;
                }
                return true;
            } catch(e) {
                return false;
            }
            return false;
        }
    </script>  -->

</div>
</body>
</html>

  



