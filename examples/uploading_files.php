<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook);

$basic = $dw
  ->newMessage()
  ->setContent("Files for you")
  ->addFile("id", "files/file.txt");
print_r($basic->send());



$mix = $dw
  ->newMessage()
  ->setContent("Files for you")
  ->addFile("id", "files/file.txt", "Cool-file-name.txt")
  ->addFile("id2", "files/image.jpg");
print_r($mix->send());
# the same as below
$mix2 = $dw
  ->newMessage()
  ->setContent("Files for you")
  ->addFiles(
    ["id", "files/file.txt", "Cool-file-name.txt"],
    ["id2", "files/image.jpg"],
    ["id3", "files/image2.jpg"],
  );
# the same as below
$mix3 = $dw
  ->newMessage()
  ->setContent("Files for you")
  ->addFiles(
    [
      "id" => "id",
      "path" => "files/file.txt",
      "name" => "Cool-file-name.txt"
    ],
    [
      "id" => "id2",
      "path" => "files/image.jpg",
    ],
    [
      "id" => "id3",
      "path" => "files/image2.jpg",
    ],
  );


?>