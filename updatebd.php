<?php


$mail = $_POST['mail'];
$name = $_POST['name'];

global $wpdb;

$wpdb->show_errors();


echo var_dump($wpdb->get_row( "SELECT * FROM `wp_limi_forwards`"));

$wpdb->print_error();
//SELECT * FROM `wp_limi_forwards`
/*
try {
    global $wpdb;

    $wpdb->get_results( "INSERT INTO `wp_limi_forwards`( `player_name`, `pay_mail`, `pay_skype`, `final_koplate`, `final_coins`, `final_rekvisitu`, `final_msg`) VALUES ('$name','$mail','','',','','')" );
} catch (Exception $e) {
    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
}


*/


echo $mail.' '.$name;
