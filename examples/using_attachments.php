<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook);

$embedImage = $dw
  ->newMessage()
  ->setImage("attachment://beautiful_image.jpg")
  ->addFile("id", "files/image2.jpg")
  ->addAttachment("id", "beautiful_image.jpg");
print_r($embedImage->send());

$basic = $dw
  ->newMessage()
  ->setTitle("Embed Title")
  ->setDescription("Embed Description")
  ->addField("Inline Field 1", "Value 1")
  ->setTimestamp()
  ->setColor("#ffffff")
  ->setImage("attachment://beautiful_image.jpg")
  ->addFile("id", "files/image.jpg")
  ->addAttachment("id", "beautiful_image.jpg");
print_r($basic->send());

$multiple = $dw
  ->newMessage()
  ->setTitle("Embed Title")
  ->setDescription("Embed Description")
  ->addField("Inline Field 1", "Value 1")
  ->setTimestamp()
  ->setColor("#ffffff")
  ->setImage("attachment://beautiful_image.jpg")
  ->setThumbnail("attachment://beautiful_image2.jpg") 
  ->addFile("id", "files/image.jpg")
  ->addFile("id2", "files/image2.jpg")
  ->addAttachment("id", "beautiful_image.jpg")
  ->addAttachment("id2", "beautiful_image2.jpg");
print_r($multiple->send());


?>