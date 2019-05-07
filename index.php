
<?php

    require_once "pdo.php";
    require_once "util.php";
    require_once "head.php";

    // if(!isset($_SESSION)) 
    // { 
        session_start(); 
    // } 

    if (!empty($_SESSION['message'])) {
        echo($_SESSION['message']);
        unset($_SESSION['message']);
    }

    $errors = [];
    $success = [];

    if ((!isset($_SESSION['user_id'])) || (!isset($_SESSION['name']))){

        echo '<a href="login.php">Please log in</a>';    

    } else {
        
        echo '&nbsp; &nbsp; &nbsp; Welcome! &nbsp; &nbsp; &nbsp; <a href="logout.php">Logout</a>' ; 

        echo('<table class="table table-striped" border="1" >'."\n");

        // profile_id, user_id, first_name, last_name, email, headline, summary

        $sql = ("SELECT * FROM Profile"); // WHERE profile_id = :pd, user_id = :ud, first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su");
        $stmt = $pdo->query($sql);
        // $stmt->execute(array(

        // ));

        echo "<tr><th>Profile Id</th><th>User Id</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Headline</th> <th>Summary</th> <th>Action</th><th>View</th>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo("<tr><td>");  
        echo(htmlentities($row['profile_id']));
        echo("</td><td>");
        echo(htmlentities($row['user_id']));    
        echo("</td><td>");
        echo(htmlentities($row['first_name']));
        echo("</td><td>");
        echo(htmlentities($row['last_name']));
        echo("</td><td>");
        echo(htmlentities($row['email']));
        echo("</td><td>");
        echo(htmlentities($row['headline']));
        echo("</td><td>");
        echo(htmlentities($row['summary']));
        echo("</td><td>");
        // echo ('<a href="view.php?profile_id='.$row['profile_id'].'">View</a>' );
        echo ('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>  |  <a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>'); 
        echo("</td><td>");
        echo('<a href="view.php?profile_id='.$row['profile_id'].'">View</a>');
        
        echo("</td></tr>\n");
        echo('</table');
        echo '<p></p>';
    }
}

    ?>
    <!-- END: View one -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yumei Leventhal index.php</title>
    <!-- bootstrap files in head.php -->
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">

</head>
<body>
<style>
        table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        }
        th, td {
        padding: 10px;
        } 
</style>

<div class="container">
    <h1> Yumei Leventhal Resume Registry</h1>
    <a href="login.php">Please log in</a>
<br>
   



<br>
 <br>

            <p>'My name is Ozymandias, king of kings;</p>
            <p>Look on my works, ye Mighty, and despair!</p>
            <p>Nothing beside remains. Round the decay</p>
            <p>Of that colossal wreck, boundless and bare</p>
            <p>The lone and level sands stretch far away.</p>
            <!-- <a href="add.php">Add New Entry</a> -->
    <br>
    <a href="add.php">Add New Entry</a>
    </div>  
</body>
</html>