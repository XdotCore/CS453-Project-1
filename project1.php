<?php
// Recieved variables from the two teacher textbooks request
    // Titles of both books
    $title1 = $_REQUEST["title1"];
    $title2 = $_REQUEST["title2"];
    // Publishers of both books
    $publisher1 = $_REQUEST["publisher1"];
    $publisher2 = $_REQUEST["publisher2"];
    // Editions of both books
    $edition1 = $_REQUEST["edition1"];
    $edition2 = $_REQUEST["edition2"];
    // Print of both books
    $printing1 = $_REQUEST["printing1"];
    $printing2 = $_REQUEST["printing2"];

    print "Yo wassup.\n$title1, $publisher1, $edition1, $printing1\n$title2, $publisher2, $edition2, $printing2";
?>