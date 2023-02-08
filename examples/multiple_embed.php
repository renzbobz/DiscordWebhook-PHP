<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook);

$embed1 = $dw
  ->newMessage()
  ->setTitle("Embed 1");
  $embed2 = $dw
  ->newMessage()
  ->setTitle("Embed 2");
  
/*
require "DiscordEmbed.php";
# If you're planning to do multiple embed,
# it's better to use DiscordEmbed class for better performance.
$de1 = (new DiscordEmbed())
  ->setTitle("DiscordEmbed")
  ->setDescription("Using DiscordEmbed class");
*/

$msg = $dw
  ->newMessage()
  ->setTitle("Main embed")
  ->addEmbed($embed1)
  ->addEmbed($embed2);
  //->addEmbed($de1);
print_r($msg->send());

$changeOrder = $dw
  ->newMessage()
  ->setTitle("Main embed")
  ->addEmbed($embed1) // <- embed1 index is 1, because the main embed is index 0
  ->addEmbed($embed2, 1); // move embed2 to index 1, then the embed1 must move to index 2, and so on
  //->addEmbed($de1);
print_r($changeOrder->send());

$replaceEmbed = $dw
  ->newMessage()
  ->setTitle("Main embed")
  ->addEmbed($embed1)
  ->addEmbed($embed2, 1, true); // replace embed1 of embed2  
  //->addEmbed($de1);
print_r($replaceEmbed->send());



?>