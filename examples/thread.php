<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook);

$newThread = $dw
  ->newThread("Thread name")
  ->setContent("Hello thread");
print_r($newThread->send());

$threadId = 0;
$msgToThread = $dw
  ->newMessage()
  ->setTitle("Hello thread")
  ->setTimestamp()
  ->setThreadId($threadId);
print_r($msgToThread->send());


?>