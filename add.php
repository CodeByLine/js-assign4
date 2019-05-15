<?php
//Make the database connection and leave it in the variable $pdo

  require_once "pdo.php";
  require_once "util.php";
  require_once "head.php";

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

    if (! isset($_SESSION['user_id'])) {
      die("ACCESS DENIED");
      return;
    }

    if( isset($_POST['cancel'])){
      header('Location: index.php');
      return;
    }

  $errors = [];
  $success = [];

  // Handle the incoming data

  if ( isset($_POST['first_name']) && isset($_POST['last_name'])
      && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

        $msg = validateProfile();
        if ( is_string($msg) ) {
          $_SESSION['error'] =  $msg;
          header("Location: add.php");
          return;
        }

        $msg = validatePos();
        if (is_string($msg)) {
          $_SESSION['error'] = $msg;
          header("Location: add.php");
          return;
        }


        // Validate position entries if present
// If a string is returned, then it's an error.

      // $    $msg = validateEds();    //Doesn't work!!!
      // if (is_string($msg)) {
      //     $_SESSION['error'] = $msg;
      //     header("Location: add.php");
      // }

// Profile validation code: moved to util.php
      
// Data is valid – time to insert
// insert profile info

    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
    VALUES ( :uid, :fn, :ln, :em, :he, :su)'); 
    $stmt->execute(array(
      ':uid' => $_SESSION['user_id'],
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':he' => $_POST['headline'],
      ':su' => $_POST['summary'])
      );
    $profile_id = $pdo->lastInsertId();
      print_r($profile_id);

    

    //  print_r($profile_id);
      //Insert the position entries
      $msg = insertPos($pdo, $profile_id); //$_REQUEST['profile_id']);// 
      if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
      } else {
        $_SESSION['success'] = 'Record updated';
        $_SESSION['message'] = "<p style = 'color:green'>Record updated.</p>\n";
            header('Location: index.php');
            return;
        
      }

  	//Insert Position entry--code below works!
// 		$rank = 1;
// 		for($i=1; $i<=9; $i++) {
//     if ( ! isset($_POST['year'.$i]) ) continue;
//     if ( ! isset($_POST['desc'.$i]) ) continue;

//     $year = $_POST['year'.$i];
//     $desc = $_POST['desc'.$i];
//     $stmt = $pdo->prepare('INSERT INTO Position
//       (profile_id, rank, year, description)
//       VALUES ( :pid, :rank, :year, :desc)');

//     $stmt->execute(array(
//     ':pid' => $profile_id,
//     ':rank' => $rank,
//     ':year' => $year,
//     ':desc' => $desc)
//     );

//     $rank++;

// }


/////////////////// BEGIN insert school 
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i])) continue;
      if ( ! isset($_POST['school'.$i])) continue;
          $year = htmlentities($_POST['year'.$i]);
          $school = htmlentities($_POST['school'.$i]);


    $stmt = $pdo->prepare("SELECT * FROM Institution WHERE name = :xyz");
    $stmt->execute(array(":xyz" => $profile_id)); //$_REQUEST["profile_id"]));
    // var_dump ($institution_id);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
            $institution_id = $row['institution_id'];

    // $stmt->execute(array(":prof" => $_REQUEST["profile_id"]));
    // $education = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
      // print_r($institution_id);  /
    }


      
  }

    $_SESSION['success'] = "<p style = 'color:green'>Profile added.</ p>\n";
    $_SESSION['message'] = "<p style = 'color:green'>Profile added.</ p>\n";
    error_log("Profile added.", 0);
    header( 'Location: index.php' ) ;
    return;
 
    
    
 
    //  $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');

    //   $stmt->execute(array(
    //     '$institution_id' => $pdo->lastInsertId() ));
      
      // $rank++;
      // var_dump($stmt); 

    }
    // $msg = insertEds($pdo, $profile_id);
    // if (is_string($msg)) {
    //   $_SESSION['error'] = $msg;
    //   header("Location: add.php");
    //   return;
    // }
/////////////// END inser education



// }
      
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Yumei Leventhal add.php</title>

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
    <h1>Add Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php

        flashMessages();

      if ( isset($_SESSION['message']) ) {
        echo $_SESSION['message']."</p>\n";
        unset($_SESSION['message']);
      }
  // // Flash pattern
  //     if ( isset($_SESSION['error']) ) {
  //       echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
  //       unset($_SESSION['error']);
  //     }
    ?>

      <form method="post">
      <p>First Name:
      <input type="text" name="first_name" size="60"/></p>
      <p>Last Name:
      <input type="text" name="last_name" size="60"/></p>
      <p>Email:
      <input type="text" name="email" size="30"/></p>
      <p>Headline:<br/>
      <input type="text" name="headline" size="80"/></p>
      <p>Summary:<br/>
      <textarea name="summary" rows="8" cols="80"></textarea></p>

      <p>Education: <input type="submit" id="addEds" value="+" autocomplete="on">
      <div id="eds_fields">
      </div>


      <p>Position: <input type="submit" id="addPos" value="+">
      <div id="position_fields">
      </div>
      </p>
      <p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
      </p>

    </form>

    <script>
        countEds = 0;
        countPos = 0;
        // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function () {
              window.console && console.log('Document ready called');
            $('#addEds').click(function (event) {
                event.preventDefault();
                if (countEds >= 9) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
            countEds++;
            window.console && console.log("Adding institutions " + countEds);
            $('#eds_fields').append(
                    '<div id="eds' + countEds + '"> \
            <p>Year: <input type="text" name="year' + countEds + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#eds' + countEds + '\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" id="school" name="school' + countEds + '" class="school" value=""/>\
            </p></div>'
                );
            $('.school').autocomplete({
                    source: "school.php"
                });
            });

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    // $(document).ready(function () {
    //   window.console && console.log('Document ready called');
      $('#addPos').click(function(event) {                //look up #addPos, then register an event
// http://api.jquery.com/event.preventdefault/
        event.preventDefault();                         // similar to "return false"
        if ( countPos >= 9) {
          alert("Maximum of nine position entries exceeded");
          return;
        }

        countPos++;
        window.console && console.log("Adding position " + countPos);
            $('#position_fields').append(        //adding html code // one long string concatenaiton
                '<div id="position' + countPos + '"> \
        <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
        <input type="button" value="-" \
            onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
        <p>Description: <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
        </p></div>');
        });
  });
    
  </script>
</div>
</body>
</html>
