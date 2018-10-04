<?php

require_once("../../../wp-load.php");

$mail = $_POST['mail'];
$name = $_POST['name'];

 global $wpdb;

$wpdb->get_results( "INSERT INTO `wp_limi_forwards`( `player_name`, `pay_mail`, `pay_skype`, `final_koplate`, `final_coins`, `final_rekvisitu`, `final_msg`) VALUES ('$name','$mail','','','','','')" );



echo $mail.' '.$name;
