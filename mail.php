<?php
require_once("../../../wp-load.php");
/*
$attachments[] = WP_CONTENT_DIR . '/uploads/file_1.zip';
$attachments[] = WP_CONTENT_DIR . '/uploads/file_2.zip';

$headers[] = 'Content-type: text/html; charset=utf-8'; // в виде массива
wp_mail('true@truemisha.ru', 'Какая-то тема', 'Какое-то сообщение', $headers,$attachments);*/

if (isset($_POST["pay_mail"])) {
    $arr = $_POST["pay_mail"] ;
    $text = isset($_POST["message"])&&trim($_POST["message"])!=""?$_POST["message"]:"HOCKEY VIRTUAL COINS";
    $subj = isset($_POST["subject"])&&trim($_POST["subject"])!=""?$_POST["subject"]:"HOCKEY VIRTUAL COINS";
   
    $htmlText =  file_get_contents ( plugin_dir_url( __FILE__ )."message.html");//"<h1>$subj</h1><p>$text</p>";
        
    $htmlText = str_replace ("{{subj}}",$subj,$htmlText);
    $htmlText = str_replace ("{{message}}",$text,$htmlText);
    
    $index = 0;
    foreach($arr as $mail){
        $headers[] = 'Content-type: text/html; charset=utf-8'; // в виде массива
        wp_mail($mail, $subj,  $htmlText, $headers);
        $index++;
        echo $index;
    }
  
}
else {
    echo "error";
}


