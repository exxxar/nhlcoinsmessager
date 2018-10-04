<?php

require_once("../../../wp-load.php");

$mail = $_POST['mail'];


global $wpdb;

$wpdb->update( 'wp_limi_forwards',
	array( 'pay_mail' => "-".$mail),
	array( 'pay_mail' => $mail )
);

echo $mail;
