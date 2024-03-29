# DiscordWebhook-PHP

Easily send/delete/update a message to discord with embeds and/or files.

## Getting started

### Installation

Download the DiscordWebhook.php file and then require it to your project and you're ready to go!

```php
require "DiscordWebhook.php";
```

### Usage

Create new instance and set your desired initial values.

```php
$dw = new DiscordWebhook($webhook);
# or
$dw = new DiscordWebhook($options);
# or
$dw = new DiscordWebhook($webhook, $options);
```

#### Options

| Name      |   Type    | Default | Description                                                      |
| --------- | :-------: | :-----: | :--------------------------------------------------------------- |
| username  |  string   |         | Bot name                                                         |
| avatarUrl |  string   |         | Bot avatar url                                                   |
| webhook   |  string   |         | Discord webhook url                                              |
| wait      |  boolean  |  false  | Wait for message to be sent, returns a message object on success |
| threadId  | snowflake |         | Send the message to that thread                                  |
| parseJSON |  boolean  |  true   | Automatically json parse body                                    |
| curlOpts  |   array   |         | Custom curl options                                              |

### Send plain message

```php
$msg = $dw
  ->newMessage("Hello")
  ->send();
# same as
$msg = $dw
  ->newMessage()
  ->setContent("Hello")
  ->send();
```

### Create new thread

```php
$msg = $dw
  ->newThread("Thread name")
  ->setContent("Hello thread!")
  ->send();
```

### Send message to thread

```php
$msg = $dw
  ->newMessage()
  ->setContent("Hello thread!")
  ->setThreadId($threadId)
  ->send();
```

### Embed message

```php
$msg = $dw
  ->newMessage()
  ->setContent("Hello world!")
  ->setTitle("Embed title")
  ->setDescription("Embed description")
  ->setRandomColor()
  ->send();
```

### Send files

```php
$msg = $dw
  ->newMessage()
  ->setContent("You can also add content and embeds too!")
  ->setTitle("Embed title")
  ->addFile("fileId", "files/file.txt")
  ->addFile("fileId2", "files/image.jpg", "Custom_name.jpg")
  ->send();
```

### Embed message with attachment files

```php
$msg = $dw
  ->newMessage()
  ->setTitle("Embed title")
  ->setImage("attachment://beautiful_image.jpg")
  ->addFile("fileId1", "files/image.jpg")
  ->addAttachment("fileId1", "beautiful_image.jpg")
  ->send();
```

### Get and create new message instance

```php
$msg = $dw->getMessage($msgId, false); // only the message id is copied
$msg2 = $dw->getMessage($msgId); // whole message is copied except for files
$msg2
  ->setContent("Content updated.")
  ->send();
```

### Update and delete message

```php
# Required option: [ "wait" => true ];
$msg = $dw
  ->newMessage()
  ->setContent("This message will be updated in 5 seconds.");
$sendRes = $msg->send(); // If success, this will automatically set the message id for you, to use update/delete/get method flawlessly (wait option must be enabled to get the message object)
sleep(5);
$updateRes = $msg
  ->setContent("Message updated. This message will be deleted in 5 seconds.")
  ->update();
sleep(5);
$deleteRes = $msg->delete();
```

[More example](https://github.com/renzbobz/DiscordWebhook-PHP/tree/master/examples)

### Response Object

| Name       |      Type       | Description                                                  |
| ---------- | :-------------: | ------------------------------------------------------------ |
| success    |     boolean     | Returns true if response code is in 2xx range                |
| body       | string \| array | Response body (auto json parse, if parseJSON option is true) |
| code       |       int       | Response code                                                |
| curl_error |     string      | Curl error message                                           |

## Methods

#### New message

```php
newMessage(?string $content);
```

#### New thread

```php
newThread(string $name);
```

#### Set thread Id

```php
setThreadId(snowflake $id);
```

#### Set Message Id

```php
setMessageId(snowflake $msgId);
```

#### Get and create new message

`$copyMsg == true` ? Copy the whole message (including embeds,attachments,bot name/avatar,etc., except for files).

`$copyMsg == false` ? Copy the message id only.

```php
getMessage(snowflake $msgId, ?bool $copyMsg = true);
```

#### Get message

```php
get(?snowflake $msgId): ResponseObject;
```

#### Send message

If response is success and (wait option is enabled or message object is detected), this will automatically set the message id.

```php
send(?string $webhook): ResponseObject;
send(?array $options): ResponseObject;
```

#### Update message

```php
update(?snowflake $msgId): ResponseObject;
```

#### Delete message

```php
delete(?snowflake $msgId): ResponseObject;
```

#### Set Bot username

```php
setUsername(string $username);
```

#### Set Bot avatar

```php
setAvatarUrl(string $avatarUrl);
```

#### Set Webhook

```php
setWebhook(string $webhook);
```

#### Set Is wait message

```php
waitMessage(?bool $wait = true);
```

#### Set Message Content

```php
setContent(string $content);
prependContent(string $content);
appendContent(string $content);
```

#### Set Message Text-to-speech

```php
setTts(?bool $tts = true);
```

#### Set Multiple Embed

```php
addEmbed(object $embed, ?int $index = null, ?bool $replace = false);
```

#### Set Files

```php
# Associative array
$file = [
  "id" => string,
  "path" => string,
  "name" => ?string,
  "type" => ?string,
];
# Indexed array
$file = [
  string $id,
  string $path,
  ?string $name,
  ?string $type
];
```

```php
addFile(associative array $file);
addFile(string $id, ?string $path, ?string $name, ?string $type);
addFiles(associative|indexed array ...$files);
```

#### Set Attachments

`$attachmentObject` - [Attachment object](https://discord.com/developers/docs/resources/channel#attachment-object)

```php
# Associative array
$attachment = [
  "id" => string,
  "filename" => string,
  ...$attachmentObject,
];
```

```php
addAttachment(associative array $attachment);
addAttachment(string $id, ?string $filename);
addAttachments(associative array ...$attachments);
```

#### Set Flag

`$flag` - [Message flags](https://discord.com/developers/docs/resources/channel#message-object-message-flags)

```php
setFlag(int $flag);
```

### Set Allowed mentions

`$allowedMentionsObject` - [Allowed mentions object](https://discord.com/developers/docs/resources/channel#allowed-mentions-object)

```php
setAllowedMentions(array $allowedMentionsObject);
```

#### Formatting

```php
toJSON(): string;
```

## Embed Methods

#### Title

```php
setTitle(string $title, ?string $url);
prependTitle(string $title);
appendTitle(string $title);
```

#### Title Url

```php
setUrl(string $url);
```

#### Description

```php
setDescription(string $description);
prependDescription(string $description);
appendDescription(string $description);
```

#### Color

`$color` can be a hex, decimal, or rgb (comma separated).

```php
setColor(int $color);
setColor(string $color);
setRandomColor();
```

#### Timestamp

```php
setTimestamp(?string $ts = date('c'));
```

#### Author

```php
# Associative array
$author = [
  "name" => string,
  "url" => ?string,
  "icon_url" => ?string,
  "proxy_icon_url" => ?string,
];
```

```php
setAuthor(string $name, ?string $url, ?string $iconUrl, ?string $proxyIconUrl);
setAuthor(associative array $author);
```

#### Footer

```php
# Associative array
$footer = [
  "text" => string,
  "icon_url" => string,
  "proxy_icon_url" => ?string,
];
```

```php
setFooter(string $text, ?string $iconUrl, ?string $proxyIconUrl);
setFooter(associative array $footer);
```

#### Image

```php
# Associative array
$image = [
  "url" => string,
  "proxy_url" => ?string,
  "height" => ?int,
  "width" => ?int,
];
```

```php
setImage(string $url, ?string $proxyUrl, ?int $height, ?int $width);
setImage(associative array $image);
```

#### Thumbnail

```php
$thumbnail = [
  # Associative array
  "url" => string,
  "proxy_url" => ?string,
  "height" => ?int,
  "width" => ?int,
];
```

```php
setThumbnail(string $url, ?string $proxyUrl, ?int $height, ?int $width);
setThumbnail(associative array $thumbnail);
```

#### Add Field

```php
# Associative array
$field = [
  "name" => string,
  "value" => ?string,
  "inline" => ?bool,
];
# Indexed array
$field = [
  string $name,
  ?string $value,
  ?bool $inline,
];
```

```php
addField(associative array $field);
addField(string $name, ?string $value, ?bool $inline);
addFields(associative|indexed array ...$fields);
```
