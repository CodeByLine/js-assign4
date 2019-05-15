<?php
// TO use: require_once "util.php";
    require_once "pdo.php";
    // session_start();

    // if(!isset($_SESSION)) 
    // { 
    //     session_start(); 
    // } 

function flashMessages() {          
    // if ( isset($_SESSION['message']) ) {
    //     echo $_SESSION['message']."</p>\n";
    //     unset($_SESSION['message']);
    //   }
    
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


    function insertPos( $pdo, $profile_id) {
	    $rank = 1;
		for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $year,
        ':desc' => $desc)
        );

        $rank++;

    }
    }

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
    // var_dump($positions);
    }
// var_dump($positions);

/////////////////// BEGIN insert school 
function insertEds($pdo, $profile_id) { 
    $rank = 1;
    for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['school'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $school = $_POST['school'.$i];

    $stmt = $pdo->prepare("SELECT * FROM Institution where name = :xyz");
    $stmt->execute(array(":xyz" => $school));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $institution_id = $row['institution_id'];
    }


// function validateEds() {
//     for($i=1; $i<=9; $i++) {
//         if ( ! isset($_POST['year'.$i])) continue;
//         if ( ! isset($_POST['school'.$i])) continue;
//         $year = $_POST['year'.$i];
//         $school = $_POST['school'.$i];
//         if ( strlen($year) == 0 || strlen($school) == 0 ) {
//             return "All fields are requred";
//         }
//         if ( ! is_numeric($year)) {
//             return "Position yyyyyear must be numeric";
//         }
//     return true;
// }


   
    $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES ( :name)');
    $stmt->execute(array(
        ':name' => $school,
    ));
    
    $institution_id = $pdo->lastInsertId();
    echo $institution_id;
    }
    $stmt = $pdo->prepare('INSERT INTO Education
            (profile_id, institution_id, year, rank)
            VALUES ( :pid, :institution, :year, :rank)');
    $stmt->execute(array(
            ':pid' => $profile_id,
            ':institution' => $institution_id,
            ':year' => $year,
            ':rank' => $rank)
    );
    $rank++;
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
    // NO closing PHP bracket "? >": 