<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook);

$msg = $dw
  ->newMessage()
  ->setContent("Hello World")
  ->setTts(); // text to speech
print_r($msg->send());

$msg2 = $dw
  ->newMessage("Hello World 2");
print_r($msg2->send());


?>