<?php

    require_once "pdo.php";
    require_once "util.php";
    require_once "head.php";
    
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

    // $errors = [];
    // $success = [];

    if ((!isset($_SESSION['user_id'])) || (!isset($_SESSION['name']))) {

      $_SESSION['message'] = "<p style = 'color:red'>Not logged in.</p>\n";
      $_SESSION['error'] = "<p style = 'color:red'>Not logged in.</p>\n";
      error_log("Not logged in.", 0);
      header( 'Location: index.php' );
      return;
  }

    if ( isset($_POST['logout']) ) {
      unset($_SESSION['email']);
      header('Location: index.php');
      return; }

    if ( isset($_POST['delete'])  && isset($_POST['profile_id'])) {

        $sql = "DELETE FROM Profile WHERE profile_id = :zip";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':zip' => $_POST['profile_id']));
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['success'] = "<p style = 'color:green'>Profile deleted.</p>\n";
        $_SESSION['message'] = "<p style = 'color:green'>Profile deleted.</p>\n";
        header( 'Location: index.php' ) ;
        return;
    }


    $stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :zip");
    $stmt->execute(array(':zip' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
      $_SESSION['message'] = "<p style = 'color:red'>Bad value for profile_id.</p>\n";
      $_SESSION['error'] = "<p style = 'color:red'>Delete failed.</p>\n";
      header( 'Location: index.php' ) ;
      return;
    } 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yumei Leventhal delete.php</title>
    <!-- bootstrap files in head.php -->

    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
</head>
<body>

<div class="container">

  <p class="alert-danger">Confirm: Deleting <?= htmlentities($row['profile_id']) ?>?</p>
  
  <form method="post">
    <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
    <input type="submit" value="Delete" name="delete"> &nbsp; &nbsp; &nbsp;
    <a href="index.php" button type="button" class="btn">Cancel</button></a>

  </form>

    </div>
</body>
</html>