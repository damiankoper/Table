<?php

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function f_date($date) {
    return date("d.m.Y", strtotime($date));
}

function genId() {
    return "" . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
}

$username = "";
$password = "";
$hostname = "";
$dbname = "";
//$link = mysqli_connect($hostname, $username, $password, $datab) or die("Error " . mysqli_error($link));
$conn = new mysqli($hostname, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");


$sms_username = "";
$sms_password = "";
$sms_sender = "";
$sms_test = false;
$sm_date_count = 5;
$sm_limit = 20;

//USTAWIENIA PODZIAŁU KWOTY DLA WNIOSKÓW
$levels = [0.20, 0.05, 0.05];
$first_level = 0.20;    //stare
$second_level = 0.05;   //stare
$third_level = 0.05;    //stare
//---
//EMAIL

$email_msg = "@pkfo.pl";
$email_from = '@pkfo.pl';

$email_headers = "From: 'Centrala PKFO' <" . $email_from . ">" . PHP_EOL;
//$email_headers .= "Cc: 'Centrala PKFO' <".$email_from.">".PHP_EOL; //kopia do nadawcy
$email_headers .= "X-Sender: 'Centrala PKFO' <" . $email_from . ">" . PHP_EOL;
$email_headers .= "X-Mailer: PHP/" . phpversion() . PHP_EOL;
$email_headers .= "X-Priority: 1" . PHP_EOL; // Urgent message!
$email_headers .= "MIME-Version: 1.0" . PHP_EOL;
$email_headers .= "Content-Type: text/html; charset=UTF-8" . PHP_EOL;

$opacity_multipler = 0.18;
//$color_rules_section = array(
//    "PKF" => "rgba(255,0,0,". $opacity_multipler*0.05 .")",
//    "PKFO" => "rgba(255,255,255,". $opacity_multipler*0.05 .")",
//    "NR" => "rgba(0,255,0,". $opacity_multipler*0.05 .")",
//    "SM" => "rgba(255,200,0,". $opacity_multipler*0.175 .")",
//);
$color_rules_section = array(
    "PKF" => "rgba(255, 0, 0,". $opacity_multipler*1 .")",
    "PKFO" => "rgba(104, 160, 221,". $opacity_multipler*1*4 .")",
    "NR" => "rgba(0,200,0,". $opacity_multipler*1 .")",
    "SM" => "rgba(252,200,0,". $opacity_multipler*1*2 .")",
);
$text_color_rules_section = array(
    "PKF" => "rgba(0, 0, 0,". 1 .")",
    "PKFO" => "rgba(0,0,0,". 1 .")",
    "NR" => "rgba(0,0,0,". 1 .")",
    "SM" => "rgba(0,0,0,". 1 .")",
);
