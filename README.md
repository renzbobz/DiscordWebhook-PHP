# DiscordWebhook-PHP (New)
Easily send embedded/plain message.

Coded on phone - 8/18/20

Updated - 12/03/20

# New update v.2
### What's new?
You can now get the array data of the embed
```php
$data = $embed->getData();
```
And this will now automatically encoded to json format and it's already unescaped slashes and unicode
```php
echo $embed; // outputs data in json format
```
You can now also make your own request
```php
$ch = curl_init($webhook);
curl_setopt_array($ch, [
  CURLOPT_HTTPHEADER => ['Content-type: application/json'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $embed // No need to json_encode, this will automatically format to json
]);
$res = curl_exec($ch);
curl_close($ch);
// display result
echo $res; 
```


# Usage
Include `DiscordWebhook.php` to your project then 
create an instance of the class.
```php
$dw = new DiscordWebhook("botName", "botAvatar.jpg", "WEBHOOK_URL");
```
You can also do this
```php
new DiscordWebhook("botName", "botAvatar.jpg");
new DiscordWebhook("botName", "WEBHOOK_URL");
new DiscordWebhook("botName");
new DiscordWebhook("botAvatar.jpg");
new DiscordWebhook("WEBHOOK_URL");
new DiscordWebhook();
# You can set the webhook later if you send a message
```

## Send embedded message 

```php
$res = $dw->newEmbed()
->setTitle("Title of embed")
->setDescription ("Description of embed")
->setColor(1752220)
->send("WEBHOOK_HERE_OPTIONAL");

# $res contains ["success" => boolean, "response" => actuall_response, "code" => status_code]
```

![Preview](images/em_s.jpg)

## Send plain message

```php
$dw->send("Message here!", "WEBHOOK_HERE_OPTIONAL");
```

![Preview](images/pm.jpg)


### You can also do this
Send embed when ready
```php
# Make your embed
$embed = $dw->newEmbed()
->setTitle("Hello discord!");

# Then send it when you're ready
if ($ready) {
  $embed->send();
}
```
Create more than one embed
```php
$embed = $dw->newEmbed()
->setTitle("Embed 1")
->send();

$embed2 = $dw->newEmbed()
->setTitle("Embed 2");

$embed3 = $dw->newEmbed()
->setTitle("Embed 3");

$embed2->send();
$embed3->send();
```

## More example
```php
$icon = "https://www.seekpng.com/png/full/20-205511_discord-transparent-staff-discord-logo-black-and-white.png";
$image = "https://discord.com/assets/f72fbed55baa5642d5a0348bab7d7226.png";
$webhook = "WEBHOOK_URL";

$dw = new DiscordWebhook("Discordy", $icon, $webhook);

$dw->newEmbed()
->setContent("Content above the embed")
->setTitle("Title of embed", "https://discordy.site")
->setDescription("Description of embed")
->setColor(1752220)
->setTimestamp(date("c", time()))
->setAuthor("Author name", "https://author.site", $icon)
->setImage($image)
->setThumbnail($icon)
->setFooter("Footer text", $icon)
->addField("Field 1", "field 1 value")
->addField("Field 2", "field 2 value")
->addField("Field 3", "field 3 value")
->send();
```

![Preview](images/e1.jpg)


