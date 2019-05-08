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
  // if ((!isset($_SESSION['user_id'])) || (!isset($_SESSION['name']))) {
  if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }


  //Chuck: Load up
    $stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :prof AND user_id = :uid");
    $stmt->execute(array(':prof' => $_REQUEST['profile_id'],
        ':uid' => $_SESSION['user_id']));
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile === false) {
        $_SESSION['error'] = 'Could not load profile';
        header('Location: index.php');
        return;
        }

// Chuck: Handle the incoming data


  if ( isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

    $msg = validateProfile();
    if ( is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);   
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

    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, 
        last_name = :ln,email=:em, headline=:he, summary=:su
        WHERE profile_id = :pid AND user_id=:uid');
    $stmt->execute(array(
        ':pid' => $_REQUEST['profile_id'],
        ':uid' => $_SESSION['user_id'],  //NOT $_REQUEST['user_id']
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'] ) );
            
    $_SESSION['success'] = 'Record updated';
    $_SESSION['message'] = "<p style = 'color:green'>Record updated.</p>\n";

        // var_dump($_REQUEST['profile_id']);
        // var_dump($_REQUEST['user_id']);

    // echo ($_POST['headline']);



///// Clear out the old education entries--replace, instead of edit     
    // $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    // $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

///// Clear out the old position entries--replace, instead of edit     
    // $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    // $stmt->execute(array(':pid' => $_REQUEST['profile_id']));



//Insert education
    $msg = insertEds($pdo, $_REQUEST['profile_id']);
    if (is_string($msg)) {
      $_SESSION['error'] = $msg;
      header("Location: add.php");
      return;
    }

//Insert the position entries
    $msg = insertPos($pdo, $_REQUEST['profile_id']);
    if (is_string($msg)) {
      $_SESSION['error'] = $msg;
      header("Location: add.php");
      return;
    }

//Insert the position entries
    // $rank = 1;
    // for ($i = 1; $i <= 9; $i++) {
    //     if (!isset($_POST['year' . $i])) continue;
    //     if (!isset($_POST['desc' . $i])) continue;
    //     $year = $_POST['year' . $i];
    //     $desc = $_POST['desc' . $i];
    // $stmt = $pdo->prepare('INSERT INTO Position
    //         (profile_id, rank, year, description)
    //     VALUES ( :pid, :rank, :year, :desc)');
    // $stmt->execute(array(
    //                 ':pid' => $_GET['profile_id'],
    //                 ':rank' => $rank,
    //                 ':year' => $year,
    //                 ':desc' => $desc)
    //         );
    //         $rank++;
    //     }
    
    $_SESSION['success'] = 'Record updated';
    $_SESSION['message'] = "<p style = 'color:green'>Record updated.</p>\n";
        header('Location: index.php');
        return;
    }

    
    $stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Bad value for user_id';
        header( 'Location: loggedin_index.php' ) ;
        return;
    }
        // print_r($_REQUEST['profile_id']);
    // echo ($_GET['profile_id']);
    
    $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $positions = $stmt->fetchAll();  //row
    if ($positions === false) {
        $_SESSION['error'] = 'Bad value for user_id in Position';
        header('Location: index.php');
        return;
    }
// Load up the position rows to be used for the other pages
    $positions = loadPos($pdo, $_REQUEST['profile_id']);

    // Education
 
// Load up the education rows to be used for the other pages

    // var_dump($education);
    // $education = loadEds($pdo, $profile_id);
    // $stmt = $pdo->prepare('SELECT * FROM Education WHERE profile_id = :prof ORDER BY rank');
    // $stmt ->execute(array(':prof' => $profile_id ));
    // $positions = $stmt->fetchAll$positions;
    // $education = array();
    // $row = $stmt->fetch(PDO::FETCH_ASSOC); 
    // while  ($education = $stmt->fetch         (PDO::FETCH_ASSOC))  {
    //     $education[] = $row;
    // }
    // return $education;
    
    $stmt = $pdo->prepare("SELECT * FROM Education where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $education = $stmt->fetchAll();
 
    
    // while  ($education = $stmt->fetch         (PDO::FETCH_ASSOC)) {
    //     $inst = $education['institution_id'];
    //     $edyear = $education['year'];
    // }

    // if ($education === false) {
    //     $_SESSION['error'] = 'Bad value for user_id in Education';
    //     header('Location: index.php');
    //     return;
    // }

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
            <input type="text" name="first_name" size="60" value="<?=  $row['first_name'] ?>"/></p>
        <p>Last Name:
            <input type="text" name="last_name" size="60" value="<?=  $row['last_name'] ?>"/></p>
        <p>Email:
            <input type="text" name="email" size="30" value="<?=  $row['email'] ?>"/></p>
        <p>Headline:<br/>
            <input type="text" name="headline" size="80" value="<?=  $row['headline'] ?>"/></p>
        <p>Summary:<br/>
            <textarea name="summary" rows="8" cols="80"><?= $row['summary'] ?></textarea>
        </p> 
<!--Begin-Ed  -->
        <p>  Education: <input type="submit" id="addEds" value="+">
            <div id="eds_fields">
            <?php
                $rank = 1;
                foreach ($education as $education) {

                    // var_dump ($education);

                    echo "<div id=\"eds" . $rank . "\">
        <p>Year: <input type=\"text\" name=\"year1\" value=\"".$education['year']."\">
        <input type=\"button\" value=\"-\" onclick=\"$('#education". $rank ."').remove();return false;\"></p>
        <text name=\"school". $rank ."\"').\" rows=\"1\" cols=\"80\">".$education['institution_id']."</text>
        </div>";
        $rank++;
        } ?>
   </div>
        


<!--End-Ed  -->
        <p>  Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
            <?php
            $rank = 1;
            foreach ($positions as $row) {
                echo "<div id=\"position" . $rank . "\">
  <p>Year: <input type=\"text\" name=\"year1\" value=\"".$row['year']."\">
  <input type=\"button\" value=\"-\" onclick=\"$('#position". $rank ."').remove();return false;\"></p>
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
                    onclick="$(\'#education' + countEds + '\').remove();return false;"></p> <p>School: <input type="text" size="80" name="edu_school1" class="school ui-autocomplete-input" value="" autocomplete="on">\
                    </p></div>');
                    });
                });
                
        </script>

        <script>
            countPos = 0;
            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function () {
                window.console && console.log('Document ready called');
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