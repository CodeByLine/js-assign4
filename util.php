<?php
// TO use: require_once "util.php";
    require_once "pdo.php";
    // session_start();

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

function flashMessages() {          
    
    if ( isset($_SESSION['error'])) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }

    if ( isset($_SESSION['success'])) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
    }
}

// a bit of utility code

function validateProfile() {
    if ( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0 ) {
        // $_SESSION['message'] = "<p style = 'color:red'>username and password are required.</p>\n";
        // $_SESSION['error'] = "<p style = 'color:red'>username and password are required.</p>\n";
        return "Username and password are required";
        header('Location: index.php');
        return;
    }

    if ( strpos($_POST['email'], '@') == false ) {
        // $_SESSION['message'] = "<p style = 'color:red'>Email address must contain @.</p>\n";
        // $_SESSION['error'] = "<p style = 'color:red'>Email address must contain @.</p>\n";
        return "Email address must contain @";
        header('Location: index.php');
        return;
    }
    return true;
}

// Look through the POST data and return true or error msg

function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i])) continue;
        if ( ! isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are requred";
        }

        if ( ! is_numeric($year)) {
            return "Position year must be numeric";
        }
    }
    return true;
}

/* NOTE: What fetchAll() does...
    $profiles = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $profile[] = $row;
    }
    */

function loadPos($pdo, $profile_id) {      
    $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
    $stmt ->execute(array(':prof' => $profile_id ));
    // $positions = $stmt->fetchAll$positions;
    $positions = array();
    // $row = $stmt->fetch(PDO::FETCH_ASSOC); 
    while  ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $positions[] = $row;
    }
    return $positions;
    }

function insertPos( $pdo, $profile_id) {
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];

      $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

      $stmt->execute(array(
        ':pid' => $profile_id, 
        // ':pid' => $_REQUEST['profile_id'],  //$profile_id: foreign key
// new add
        ':rank' => $rank,
        ':year' => $year,
        ':desc' => $desc)
      );
    $rank++;
     }
     return true;
}

/////////////////// BEGIN insert school 
function insertEds($pdo, $profile_id) { 
    $rank = 1;
    for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edyear'.$i]) ) continue;
    if ( ! isset($_POST['school'.$i]) ) continue;
    $edyear = $_POST['edyear'.$i];
    $school = $_POST['school'.$i];

    $stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
    $stmt->execute(array( ':prefix' => $_REQUEST['institution_id']."%"));
    // $stmt->execute(array(":xyz" => $school));
    $retval = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $retval[] = $row['name'];
    }
    
    echo(json_encode($retval, JSON_PRETTY_PRINT));


    $stmt = $pdo->prepare("SELECT * FROM Institution where name = :xyz");
            $stmt->execute(array(":xyz" => $school));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $institution_id = $row['institution_id'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES ( :name)');
        $stmt->execute(array(
            ':name' => $school,
        ));
        $institution_id = $pdo->lastInsertId();
    }
    $stmt = $pdo->prepare('INSERT INTO Education
(profile_id, institution_id, year, rank)
VALUES ( :pid, :institution, :edyear, :rank)');
    $stmt->execute(array(
            ':pid' => $profile_id,
            ':institution' => $institution_id,
            ':edyear' => $edyear,
            ':rank' => $rank)
    );
    $rank++;
}

function validateEds() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edyear'.$i])) continue;
        if ( ! isset($_POST['school'.$i])) continue;
        $year = $_POST['edyear'.$i];
        $school = $_POST['school'.$i];
        if ( strlen($edyear) == 0 || strlen($school) == 0 ) {
            return "All fields are requred";
        }
        if ( ! is_numeric($edyear)) {
            return "Position year must be numeric";
        }
    return true;
}

    // $retval = array();
    // while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    //     $retval[name] = $row['name'];
    //     }
    // echo(json_encode($retval, JSON_PRETTY_PRINT));
    //     }
    // }

function loadEds($pdo, $profile_id) {      
    $stmt = $pdo->prepare('SELECT * FROM Education WHERE profile_id = :prof ORDER BY rank');
    $stmt ->execute(array(':prof' => $profile_id ));
    // $positions = $stmt->fetchAll$positions;
    $education = array();
    // $row = $stmt->fetch(PDO::FETCH_ASSOC); 
    while  ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $education[] = $row;
    }
    return $education;
    }
}
}
// function doValidate() {
//         console.log('Validating...');
  
//     try {
//         pw = document.getElementById('id_1723').value;
//         console.log("Validating pw="+pw);
   
//         if (pw == null || pw == "") {
  
//           alert("Both fields must be filled out");
//           return false;
//         }  
//           return true;
//       } catch(e) {
//         return false;   
//       }
//         return false;
//       }
//     }


// <script>
//   function addPos() {
//     var div = document.createElement('div');
//     div.className = 'row';
//     div.innerHTML =
//         '<input type="text" name="position" value="" />\
//         <input type="text" name="value" value="" />\
//         <label> <input type="checkbox" name="check" value="1" /> Checked? </label>\
//         <input type="button" value="-" onclick="removeRow(this)">';

//     document.getElementById('addpos').appendChild(div);
//     }

//     function removeRow(input) {
//         document.getElementById('pos').removeChild(input.parentNode);
//     }
//     </script>

// <script>
// function validatePos() {

// countPos = 0;
// $(document).ready(function() {
//   window.console && console.log('Document ready called');
//   $('#addPos').click(function(event){
//     event.preventDefault();
//     if ( countPos >= 9) {
//       alert("Maximum of nine position entries exceeded");
//       return;
//     }

//     countPos++;
//     window.console && console.log("Adding position "+countPos);
//     $('#position_fields').append(
//       <div id="position'+countPos+'"> \ 
//       <p>year: <input type="text" name="year'+countPos+'" value=" /> \ 
//       <input type="button" value="-" \ 
//             onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \ 
//             <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
//             </div>');
//   });
// }
// </script>


// NO closing PHP bracket "? >": 

// It's entirely optional but including it provides the opportunity to slip whitespace into the output by accident. If you do that in a file that you include or require before you try to output headers then you'll break your code.
    