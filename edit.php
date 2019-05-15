<?php
  require_once "pdo.php";
  require_once "util.php";    
  require_once "head.php";
  
  if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

  if (isset($_SESSION['user_id']) == false) {
    die("ACCESS DENIED");
    return;
  }

  if( isset($_POST['cancel'])){
    header('Location: index.php');
    return;
  }

  if ( isset($_POST['logout'])) {
      unset($_SESSION['user_id']);
      unset($_SESSION['name']);
      header('Location: index.php');
      return; }

  if (!empty($_SESSION['message'])) {
      echo $_SESSION['message'];
      unset($_SESSION['message']);
  }        

  $errors = [];
  $success = [];

///////
// Make sure the REQUEST parameter is present
  if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }


// Chuck: Handle the incoming data

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

  if ( isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

    $msg = validateProfile();
    if ( is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]); 
        $profile_id = $_REQUEST["profile_id"];
        // line above differs fr add.php; need the param for later
        return;
    }

// Validate position entries if present
    $msg = validatePos();
    if ( is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }


    // $msg = validateEds();
    // if ( is_string($msg)) {
    //     $_SESSION['error'] = $msg;
    //     header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    //     return;
    // }
//validate Eds
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i])) continue;
        if ( ! isset($_POST['school'.$i])) continue;
        $year = $_POST['year'.$i];
        $school = $_POST['school'.$i];
        if ( strlen($year) == 0 || strlen($school) == 0 ) {
            $_SESSION['error'] = "<p style = 'color:red'>Year must be numeric.</p>\n";
            header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
            // return;
            return "All fields are requred";
        }
        if ( ! is_numeric($year)) {
            $_SESSION['message'] = "<p style = 'color:red'>Year must be numeric.</p>\n";
            $_SESSION['error'] = "<p style = 'color:red'>Year must be numeric.</p>\n";
            header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
            return;
        }
    return true; 
    }
// echo $profile_id;
// print_r($profile_id);


//Update profile
    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, 
        last_name = :ln,email=:em, headline=:he, summary=:su
        WHERE profile_id = :pid AND user_id=:uid');
    $stmt->execute(array(
        ':pid' => $_REQUEST['profile_id'], //$profile_id,   // 
        ':uid' => $_SESSION['user_id'],  //NOT $_REQUEST['user_id']
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'] ) );

    // $_SESSION['success'] = 'Record updated';
    // $_SESSION['message'] = "<p style = 'color:green'>Record updated.</p>\n";
    // header("Location: view.php");
    // return;   //Breaks the code


      //Insert the position entries
      $msg = insertPos($pdo, $_REQUEST['profile_id']);// $profile_id); //
      
      if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php");
        return;
      } else {
        $_SESSION['success'] = 'Record updated';
        $_SESSION['message'] = "<p style = 'color:green'>Record updated.</p>\n";
            header('Location: view.php');
            return;      
      }

    

    //Insert education
    $rank = 1;
    for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['school'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $school = $_POST['school'.$i];

    $stmt = $pdo->prepare('INSERT INTO Education
        (profile_id, institution_id, year, rank)
        VALUES ( :pid, :institution, :year, :rank)');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':institution' => $institution_id,
        ':year' => $year,
        ':rank' => $rank)
        );
    $msg = insertEds($pdo, $_REQUEST['profile_id']);
    if (is_string($msg)) {
      $_SESSION['error'] = $msg;
      header("Location: add.php");
      return;
        }
    }
    }

}     /////  The First Major "if" statement//Chuck: Load up


// Load up education:
    $stmt = $pdo->prepare("SELECT * FROM Education join Institution on Education.institution_id = Institution.institution_id where profile_id = :prof ORDER BY rank");
    $stmt->execute(array(":prof" => $_REQUEST["profile_id"]));
    $education = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo ($_REQUEST["profile_id"]);   // Works
 //   echo "Education1: ";    echo " ### ";  /////////////////////////////////////
 //   var_dump($education);

    if ($education === false) {
        echo "<p style = 'color:red'>Edication NOT loade.</p>\n";
    } else { 
        echo "<p style = 'color:green'>Edication loaded.</p>\n";
 //       echo "Education2: ";    echo " ### ";     ///////////////////////////////////////////////
  //      var_dump($education);
        // $_SESSION['error'] = 'Bad value for user_id in Education';
        // header('Location: index.php');
        // return;
    }

// Load up positions
    $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz ORDER BY rank");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));    // $profile['profile_id']));
    
    $positions = $stmt->fetchAll();  //row
    // echo $profile['profile_id'];
    // echo "Positions2"; echo " ### "; var_dump($positions);    //////////////////

     // Load up the position to be used for the other pages
     $positions = loadPos($pdo, $_REQUEST['profile_id']);
    
    if ($positions === false) {
        echo "<p style = 'color:red'>Position NOT loaded.</p>\n";
        $_SESSION['error'] = 'Bad value for user_id in Position';
        header('Location: index.php');
        return;
    } else {
        echo "<p style = 'color:green'>Positions loaded for other pages.</p>\n";

    }


  //Chuck: Load up  -- Working
    $stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :prof");
    $stmt->execute(array(':prof' => $_REQUEST['profile_id']
        ));
    $profiles = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profiles === false) {
        $_SESSION['error'] = 'Could not load profile';
        echo "<p style = 'color:red'>Profile NOT loaded.</p>\n";
        header('Location: index.php');
        return;
        } else {
            echo "<p style = 'color:green'>Profile loaded.</p>\n";
        }
// // /// Clear out the old education entries--replace, instead of edit   

$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
$stmt->execute(array(':pid' => $_REQUEST['profile_id']));

// // /// Clear out the old position entries--replace, instead of edit     
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

// // /// Remove profile entries: 

// $stmt = $pdo->prepare('DELETE FROM Profile WHERE user_id=:uid');
// $stmt->execute(array(':uid' => "1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yumei Leventhal edit.php</title>
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
    <h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <p>First Name:
            <input type="text" name="first_name" size="60" value="<?=  $profiles['first_name'] ?>"/></p>
        <p>Last Name:
            <input type="text" name="last_name" size="60" value="<?=  $profiles['last_name'] ?>"/></p>
        <p>Email:
            <input type="text" name="email" size="30" value="<?=  $profiles['email'] ?>"/></p>
        <p>Headline:<br/>
            <input type="text" name="headline" size="80" value="<?=  $profiles['headline'] ?>"/></p>
        <p>Summary:<br/>
            <textarea name="summary" rows="8" cols="80"><?= $profiles['summary'] ?></textarea>
        </p> 
<!--Begin-Ed  -->
        <p>  Education: <input type="submit" id="addEds" value="+">
            <div id="eds_fields">
            <?php
                $rank = 1;
                foreach ((array) $education as $edu) {

                    // var_dump ($education);
                    echo "<div id=\"eds" . $rank . "\">
        <p>Year: <input type=\"text\" name=\"year1\" value=\"".$edu['year']."\">
        <input type=\"button\" value=\"-\" onclick=\"$('#education". $rank . "').remove();return false;\"></p>
        <text name=\"school". $rank ."\"').\" rows=\"1\" cols=\"80\">".$edu['name']."</text>
        </div>";
            $rank++;
        } ?>
        
   </div></p>
    
<!--End-Ed  -->

<!-- Begin-Pos -->
        <p>  Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
            <?php
            $rank = 1;
            foreach ((array) $position as $row) {
                echo "<div id=\"position" . $rank . "\">
        <p>Year: <input type=\"text\" name=\"year1\" value=\"".$row['year']."\">
        <input type=\"button\" value=\"-\" onclick=\"$('#position" . $rank . "').remove();return false;\"></p>
        <textarea name=\"desc". $rank ."\"').\" rows=\"8\" cols=\"80\">".$row['description']."</textarea>
</div>";
            $rank++;
            } ?>
        </div>
        </p>
        <p>
        <input type="submit" value="Save">
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
            <p>Year: <input type="text" name="edyear' + countEds + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#eds' + countEds + '\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" id="school" name="school' + countEds + '" class="school" value=""/>\
            </p></div>'
                );
                $('.school').autocomplete({
                    source: "school.php"
                });
            });
                
            // countPos = 0;
            // // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            // $(document).ready(function () {
            //     window.console && console.log('Document ready called');
            $('#addPos').click(function (event) {
                    // http://api.jquery.com/event.preventdefault/
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }

            countPos++;
            window.console && console.log("Adding position " + countPos);
                $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
                });
            });
        </script>
</div>
</body>
</html>