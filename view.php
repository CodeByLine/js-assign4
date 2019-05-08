
<?php
    require_once "pdo.php";
    require_once "util.php";
    require_once "head.php";

    // top: individual view; bottom: view all


    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
    
    if (!empty($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
      }
      
      $errors = [];
      $success = [];

    // if (isset($_SESSION['name']) == false) {
    //     die('Not logged in');
    // } 

    
    $sql = ("SELECT * FROM Profile WHERE profile_id = :xyz"); 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":xyz" => $_GET['profile_id']));    //MUST BE 'GET' here
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // echo "#row###";
    // var_dump( $row); 
    // echo "#row###";
    
    $profile_id = $row['profile_id'];
    echo "<h1>" . "Profile information for " . htmlentities($row['first_name']) . "</h1>";
    // echo "<h2>" . $txt1 . "</h2>";
    echo "<br>";
    echo "<p><strong>" . "First Name: </strong>" . $row['first_name'] . "</p>";
    // echo ("\n");
    echo "<p><strong>" . "Last Name: </strong>" . $row['last_name']."</p>";
    // var_dump($row);
    echo "<p><strong>" . "Email: </strong>" . $row['email'] . "</p>";
    echo "<p><strong>" . "Headline: </strong>" . $row['headline'] . "</p>";
    echo "<p><strong>" . "Summary: </strong>" . $row['summary'] . "</p>";
    


    // echo ("Positions: ".$row['position']."&nbsp;&nbsp; &nbsp;");
    
    $sql = "SELECT * FROM Position WHERE profile_id = :xyz";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':xyz' => $profile_id));   // $_GET['profile_id']));
    // $position = $stmt->fetch(PDO::FETCH_ASSOC);
    while ($position = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<p><strong>";
    echo ("Positions: </strong>");
   
    // var_dump($position); 

    // echo "<p>" . "Positions: " . "</p>";
   
    echo ($position['year']. "\n");
    echo "</p><strong>";
    echo ("Description: </strong>");
    echo ($position['description']);
    // echo ('<li>'."&nbsp;&nbsp; &nbsp; $position['year']".'</li>');
    // echo ('<li>'."&nbsp;&nbsp; &nbsp; $position['description']".'</li>');
}
 ////
    $stmt = $pdo->prepare('SELECT * FROM Education WHERE profile_id = :prof ORDER BY rank');
    $stmt ->execute(array(':prof' => $profile_id ));
    // $education = array();
    // $education = $stmt->fetch     (PDO::FETCH_ASSOC);
    // $edrow = $stmt->fetch      (PDO::FETCH_ASSOC);
    // var_dump($education);
    // var_dump($edrow);
    // echo($education['institution_id']);
    // echo "##";
   
    // foreach ($education as $edrow) {
    //     echo('<li>'.$education['year'].':'.$education['institution_id'].'</li>');
    // }
   
//  $edrow = $stmt->fetch(PDO::FETCH_ASSOC); 
    while  ($education = $stmt->fetch         (PDO::FETCH_ASSOC)) {
        echo "<p><strong>";
        echo ("Education: </strong></p>");
        echo ("<strong>Institution_ID: </strong>");
        echo ($education['institution_id']);
        echo "</p><strong>";
        echo ("Year: </strong>");
        echo ($education['year']. "\n");

    }
    
////// BEGIN: View All

    $stmt = $pdo->query("SELECT * FROM Profile");
    echo('<table class="table table-striped" border="1" >'."\n");

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
    echo ('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>  |  ' );

    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td><td>");
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">View</a>');

    echo("</td></tr>\n");
    echo('</table');
    echo '<p></p>';
    } 

?>


<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yumei Leventhal view.php</title>
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
            <h1><center>View Profile</center></h1>
            <br>
        
        
        <!-- BEGIN: View one  -->
        
        <a href="index.php">Back to Index</a>
        <br><br>
        

            
        <?php
               // Flash pattern
            if ( isset($_SESSION['error']) ) {
              echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
              unset($_SESSION['error']);
            }
            
        
                if ((!isset($_SESSION['id'])) || (!isset($_SESSION['name']))){
                    echo '<a href="login.php">'; echo "Login"; echo'</a>&nbsp;&nbsp;&nbsp;&nbsp;'; echo '<a href="view.php">'; echo ("View All");echo'</a>';
                } else {
                    
                    echo '<a href="add.php">Add a new resume </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    echo '<a href="logout.php">Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
         
                }
           
    ?>
   <!-- END: View all -->     
        
            <br>
            <br>
        
    </div>  
    </body>
    </html>