# DiscordWebhook-PHP
Easily send embedded/plain message.

Coded on phone - 8/16/20

# Usage
Include `DiscordWebhook.php` to your project then 
create an instance of the class.
```php
$dw = new DiscordWebhook("botName", "botAvatar.jpg", "WEBHOOK_URL");
```
You can also do this
```php
new DiscordWebhook("botName", "botAvatar");
new DiscordWebhook("botName", "WEBHOOK_URL");
new DiscordWebhook("botName");
new DiscordWebhook("botIcon");
new DiscordWebhook("WEBHOOK_URL");
new DiscordWebhook();
# You can set the webhook later if you a send message
```

## Send embedded message 

##### Single embed
```php
// You need to pass an ID to access your embed object because you can also create more embed object.
// Embed Id starts with #
$embed = $dw->embed("#embedId");

// and now set what you want
$embed["title"] = "Title of embed";
$embed["description"] = "Description of embed";
$embed["color"] = "";

// then push the embed
$dw->push("#embedId", $embed);

// and finally send it
$res = dw->send("#embedId");
// $res contains ["success" => boolean, "response" => actual_response_from_discord, "statusCode" => http_code(200)]

// You can also use callback
$dw->send("#embedId", function($success, $response, $statusCode) {
 // Do something
});

// and also you can set webhook
$dw->send("#embedId", "WEBHOOK_URL", function($success, $response, $statusCode) {
  // Do something
});
```

##### Multiple embed
```php
$embedLength = 2; // 2 embedded message
$embed = $dw->embed("#embedId", $embedLength); // 2 object is set

$e1 = $embed[0]; // Access first embed
$e1["title"] = "Title embed of first embed";

$e2 = $embed[1]; // Access second embed
$e2["title"] = " "Title embed of second embed";

$dw->push("#embedId", $embed);

$dw->send("#embedId");
```

## Send plain message

##### Shorthand method
```php
$dw->send("Message here!");
```

##### Normal method
```php
$msg = dw->embed("#msg");
$msg["content"] = "Message here!";
$dw->push("#msg", $msg);
$dw->send("#msg");
```
