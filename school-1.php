<?php
// require_once "pdo.php";
// require_once "util.php";    
require_once "head.php";

  sleep(2);
  header('Content-Type: application/json; charset=utf-8');
//   $stuff = array('first' => 'first thing', 'second' => 'second thing');
//   echo(json_encode($stuff));
    $stuff = array[
    "Universit\u00e9 Paul Sabatier III",
    "University",
    "University of AAA",
    "University of Aberdeen",
    "University of Adelaide",
    "University of BBB",
    "University of California",
    "University of Cambridg",
    "University of Cambridg modified",
    "University of Cambridge",
    "University of Cambridge MODFy",
    "University of ctw",
    "university of delhi",
    "University of Lag",
    "University of Manitoba",
    "University of Michigan",
    "University of Oxford",
    "university of school",
    "University of Virginia",
    "University of Wisconsin-Milwaukee"
    ];
    echo(json_encode($stuff));