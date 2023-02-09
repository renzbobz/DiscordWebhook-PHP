<?php

require "../DiscordWebhook.php";

$webhook = "";
$dw = new DiscordWebhook($webhook, [
  "wait" => true,
]);

# ---- UPDATE and DELETE message example -------- #

$msg = $dw
  ->newMessage()
  ->setContent("This message will be updated in 5 seconds.");
$msg->send(); // (wait option must be enabled) If success, this will automatically set the message id for you, to use update and delete flawlessly
sleep(5);
$msg
  ->setContent("Message updated. This message will be deleted in 5 seconds.")
  ->update();
sleep(5);
$msg->delete();

return; // remove this line if you already set the msgId for examples below

// or you can also just set the message id
$msgId = 0;
$dw
  ->newMessage()
  ->setContent("Message updated.")
  ->setMessageId($msgId)
  ->update();
// or
$dw
  ->newMessage()
  ->setContent("Message updated 2.")
  ->update($msgId);

$dw
  ->delete($msgId);

# ---- GET message example -------- #

$msgId = 0;
# (option 1) just fetches the msg then returns response object
$res = $dw->get($msgId);

# (option 2) fetches the msg, then copies the whole message(except for files), and returns a new message instance, ready to use
$msg = $dw->getMessage($msgId);
$msg
  ->setContent("Content updated.")
  ->send();

# or just copy the message id
$msg = $dw->getMessage($msgId, false)
  ->setFooter("Timestamp updated")
  ->setTimestamp()
  ->update();


?>
