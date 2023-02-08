<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook);

$basic = $dw
  ->newMessage()
  ->setTitle("Embed title")
  ->setDescription("Embed description");
print_r($basic->send());

$withFields = $dw
  ->newMessage()
  ->setTitle("Embed title")
  ->setDescription("Embed description")
  ->addField("Inline Field 1", "Value 1", true)
  ->addField("Inline Field 2", "Value 2", true)
  ->addField("Field 1", "Value 1")
  ->addField("Field 2", "Value 2");
print_r($withFields->send());

$imgUrl = "https://mpost.io/wp-content/uploads/image-34-99.jpg";
$advance = $dw
  ->newMessage()
  ->setAvatar($imgUrl)
  ->setUsername("Bot Name")
  ->setTitle("Embed Title", "https://title.url")
  ->setDescription("Embed Description")
  ->addField("Inline Field 1", "Value 1", true)
  ->addField("Inline Field 2", "Value 2", true)
  ->addField("Inline Field 3", "Value 3", true)
  ->setAuthor("Author Name", "https://author.url", $imgUrl)
  ->setFooter("Footer Text", $imgUrl)
  ->setImage($imgUrl)
  ->setThumbnail($imgUrl)
  ->setTimestamp()
  ->setColor("#ffffff");
print_r($advance->send());


$multipleFields = $dw
  ->newMessage()
  ->addField("Name", "Value", true)
  ->addField("Name 2", "Value 2", true);
# same as below
$multipleFields2 = $dw
  ->newMessage()
  ->addFields(
    ["Name", "Value", true],
    ["Name 2", "Value 2", true],
  );
# same as below
$multipleFields3 = $dw
  ->newMessage()
  ->addFields(
    [
      "name" => "Name",
      "value" => "Value",
      "inline" => true,
    ],
    [
      "name" => "Name 2",
      "value" => "Value 2",
      "inline" => true,
    ],
  );


?>
