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

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {

        $_SESSION['error'] = "<p style = 'color:red'>All values are required.</p>\n";
        $_SESSION['message'] = "<p style = 'color:red'>All values are required.</p>\n";
        error_log("All values are required.", 0);
        header("Location: add.php");
        return;
    } elseif (strpos($_POST['email'], '@') == false) {
      $_SESSION['error'] = 'Email must have an at-sign';
      $_SESSION['message'] = "<p style = 'color:red'>Email must have an at-sign.</p>\n";
      header("Location: add.php");
      return;

    } else { 

// Data validation code: moved to util.php

// Validate position entries if present
// If a string is returned, then it's an error.

  $msg = validatePos();
  if (is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }
    
// Data is valid – time to insert

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


/////////////////// BEGIN insert school 

    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['school'.$i]) ) continue;
      $year = $_POST['year'.$i];
      $inst = $_POST['school'.$i];

      $stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
      $stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));
      $retval = array();
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $retval[] = $row['name'];
          }
      echo(json_encode($retval, JSON_PRETTY_PRINT));

    //  $stmt = $pdo->prepare('INSERT INTO Education (profile_id, rank, year, institution_id) VALUES ( :pid, :rank, :year, :inst_id)');

    //   $stmt->execute(array(
    //     ':pid' => $profile_id, 
    // //$profile_id: foreign key
    //     ':rank' => $rank,
    //     ':year' => $year,
    //     ':inst_id' => $_REQUEST['institution_id'])
    //   );
    // $rank++;
    //  }

    }
/////////////// END inser education


//Insert the position entries
    $msg = insertPos($pdo, $_REQUEST['profile_id']);
    if (is_string($msg)) {
      $_SESSION['error'] = $msg;
      header("Location: add.php");
      return;
    }

//Insert the position entries
//     $rank = 1;
//     for($i=1; $i<=9; $i++) {
//       if ( ! isset($_POST['year'.$i]) ) continue;
//       if ( ! isset($_POST['desc'.$i]) ) continue;
//       $year = $_POST['year'.$i];
//       $desc = $_POST['desc'.$i];

//       $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

//       $stmt->execute(array(
//         ':pid' => $profile_id, 
//         // ':pid' => $_REQUEST['profile_id'],  //$profile_id: foreign key
// // new add
//         ':rank' => $rank,
//         ':year' => $year,
//         ':desc' => $desc)
//       );
//     $rank++;
//      }
    
    $_SESSION['success'] = "<p style = 'color:green'>Profile added.</ p>\n";
    $_SESSION['message'] = "<p style = 'color:green'>Profile added.</ p>\n";
    error_log("Profile added.", 0);
    header( 'Location: index.php' ) ;
    return;
  }
}

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
  // Flash pattern
      if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
      }
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

      <p>Education: <input type="submit" id="addEd" value="+">
      <div id="education_fields">
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
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    $(document).ready(function () {
      window.console && console.log('Document ready called');
      $('#addEd').click(function(event) {          
        //look up #addPos, then register an event
// http://api.jquery.com/event.preventdefault/
        event.preventDefault();                         // similar to "return false"
        if ( countEds >= 9) {
          alert("Maximum of nine institution entries exceeded");
          return;
        }

        countEds++;
        window.console && console.log("Adding institutions " + countEds);
          //adding html code // one long string concatenation
            $('#education_fields').append(        
              '<div id="edution' + countEds + '"> \
              <p>Year: <input type="text" name="year' + countEds + '" value="" /> \
              <input type="button" value="-" \
              onclick="$(\'#education' + countEds + '\').remove();return false;"></p> <p>School: <input type="text" size="80" name="edu_school1" class="school ui-autocomplete-input" value="" autocomplete="off">\
              </p></div>');
            });
          });
          
  </script>

  <script>

    countPos = 0;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    $(document).ready(function () {
      window.console && console.log('Document ready called');
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
