<?php

class DiscordWebhook {

  # DiscordWebhook-PHP
  # github.com/renzbobz
  # 2/8/23

  public $webhook = null;
  public $parseJSON = true;
  public $curlOpts = [];
  
  //? Query string params
  public $wait = false;
  public $threadId = null;
  
  //? Message props
  public $content = null;
  public $username = null;
  public $avatarUrl = null;
  public $tts = false;
  public $embeds = [];
  public $allowedMentions = [];
  public $attachments = [];
  public $flags = null;
  public $threadName = null;
  
  public $files = [];
  public $embed = [];
  private $_offsetIndex = 0;

  public function __construct($webhook, $opts=[]) { 
    $opts = $webhook && !$opts ? $webhook : [ "webhook" => $webhook, ...$opts ];
    $this->_setOpts($opts); 
  }
  public function toJSON() { return $this->_getData(); }

  //! Helpers
  private function _setOpts($opts) {
    if (is_string($opts)) {
      $this->webhook = $opts;
    } else if (is_array($opts)) {
      foreach ($opts as $key => $val) {
        $this->{$key} = $val;
      }
    }
  }

  private function _resolveColor($clr) {
    if (is_string($clr)) {
      if ($clr == 'random') return rand(0x000000, 0xFFFFFF);
      if ($clr[0] == '#') $clr = substr($clr, 1);
      if (preg_match('/,/', $clr)) $clr = sprintf('%02x%02x%02x', ...explode(',', $clr));
      $clr = hexdec($clr);
    }
    return $clr;
  }

  private function _setPropArrayValue($prop, $firstArg, $structuredVal) {
    if (is_array($firstArg)) {
      $this->{$prop} = $firstArg;
    } else {
      $this->{$prop} = $structuredVal;
    }
  }

  private function _silentJSONParse($str) {
    $json = json_decode($str, true);
    if (json_last_error() != JSON_ERROR_NONE) {
      return $str;
    }
    return $json;
  }

  private function _arrayAdderHandler($name, $values) {
    foreach ($values as $value) {
      if (!$value) continue;
      // indexed array
      if (isset($value[0])) {
        $this->{$name}(...$value);
      // associative array
      } else {
        $this->{$name}($value);
      }
    }
  }

  private function _getData() {
    $embed = $this->embed;
    $embeds = $this->embeds;
    if ($embed || $embeds) {
      // sort embeds by key first before adding the main embed so it doesn't change the order
      ksort($embeds);
      if ($embed) array_splice($embeds, $this->_offsetIndex, 0, [$embed]);
    }
    $data = [
      "content" => $this->content,
      "username" => $this->username,
      "avatar_url" => $this->avatarUrl,
      "tts" => $this->tts,
      "allowed_mentions" => $this->allowedMentions,
      "flags" => $this->flags,
      "embeds" => $embeds,
      "attachments" => $this->attachments,
      "thread_name" => $this->threadName,
    ];
    return json_encode($data);
  }

  private function _getFormData() {
    $files = [];
    foreach ($this->files as $id => $file) {
      $snowflake = $this->_getFileSnowflakeById($id);
      $files["files[$snowflake]"] = curl_file_create($file["path"], $file["type"], $file["name"]);
    }
    $data = [
      "payload_json" => $this->_getData(),
    ] + $files;
    return $data;
  }

  private function _setEmbed($key, $value, $append=false) {
    return $append ? $this->embed[$key][] = $value : $this->embed[$key] = $value;
  }
  private function _getEmbed($key) {
    return $key ? $this->embed[$key] : $this->embed;
  }
  private function _setEmbedArrayValue($key, $firstArg, $structuredVal) {
    return $this->_setEmbed($key, is_array($firstArg) ? $firstArg : $structuredVal);
  }

  private function _getFileSnowflakeById($id) {
    if (isset($this->files[$id])) {
      return $this->files[$id]["snowflake"];
    } else {
      throw new Exception("No file found with the id of ($id). Make sure you add the file first with the id of ($id).");
    }
  }

  public function setWebhook($webhook) {
    $this->webhook = $webhook;
    return $this;
  }

  # Bot profile
  public function setUsername($username) {
    $this->username = $username;
    return $this;
  }
  public function setAvatar($avatarUrl) {
    $this->avatarUrl = $avatarUrl;
    return $this;
  }

  # Query string params
  public function setThreadId($id) {
    $this->threadId = $id;
    return $this;
  }
  public function waitMessage($wait) {
    $this->wait = $wait;
    return $this;
  }

  # Clone
  public function newMessage($message=null) {
    $cloned = clone $this;
    if ($message) $cloned->setContent($message);
    return $cloned;
  }
  public function newThread($name=null) {
    $cloned = $this->newMessage();
    $cloned->threadName = $name;
    return $cloned;
  }

  # Msg content
  public function setContent($content) {
    $this->content = $content;
    return $this;
  }
  public function prependContent($content) {
    $this->content = $content . $this->content;
    return $this;
  }
  public function appendContent($content) {
    $this->content .= $content;
    return $this;
  }

  # Msg content text to speech
  public function setTts($tts=true) {
    $this->tts = $tts;
    return $this;
  }

  # Embed title
  public function setTitle($title, $url=null) {
    $this->_setEmbed("title", $title);
    if ($url) $this->setUrl($url);
    return $this;
  }
  public function prependTitle($title) {
    $this->_setEmbed("title", $title . $this->_getEmbed("title"));
    return $this;
  }
  public function appendTitle($title) {
    $this->_setEmbed("title", $this->_getEmbed("title") . $title);
    return $this;
  }

  # Embed url
  public function setUrl($url) {
    $this->_setEmbed("url", $url);
    return $this;
  }

  # Embed description
  public function setDescription($desc) {
    $this->_setEmbed("description", $desc);
    return $this;
  }
  public function prependDescription($desc) {
    $this->_setEmbed("description", $desc . $this->_getEmbed("description"));
    return $this;
  }
  public function appendDescription($desc) {
    $this->_setEmbed("description", $this->_getEmbed("description") . $desc);
    return $this;
  }

  # Embed color
  public function setColor($clr) {
    $this->_setEmbed("color", $this->_resolveColor($clr));
    return $this;
  }
  public function setRandomColor() {
    $this->_setEmbed("color", $this->_resolveColor("random"));
    return $this;
  }

  # Embed timestamp
  public function setTimestamp($ts=null) {
    if (!$ts) $ts = date('c');
    $this->_setEmbed("timestamp", $ts);
    return $this;
  }

  # Embed author
  public function setAuthor($name, $url=null, $iconUrl=null, $proxyIconUrl=null) {
    $this->_setEmbedArrayValue("author", $name, [
      "name" => $name,
      "url" => $url,
      "icon_url" => $iconUrl,
      "proxy_icon_url" => $proxyIconUrl,
    ]);
    return $this;
  }

  # Embed thumbnail
  public function setThumbnail($url, $proxyUrl=null, $height=null, $width=null) {
    $this->_setEmbedArrayValue("thumbnail", $url, [
      "url" => $url,
      "proxy_url" => $proxyUrl,
      "height" => $height,
      "width" => $width,
    ]);
    return $this;
  }

  # Embed image
  public function setImage($url, $proxyUrl=null, $height=null, $width=null) {
    $this->_setEmbedArrayValue("image", $url, [
      "url" => $url,
      "proxy_url" => $proxyUrl,
      "height" => $height,
      "width" => $width,
    ]);
    return $this;
  }

  # Embed footer
  public function setFooter($text, $iconUrl=null, $proxyIconUrl=null) {
    $this->_setEmbedArrayValue("footer", $text, [
      "text" => $text,
      "icon_url" => $iconUrl,
      "proxy_icon_url" => $proxyIconUrl
    ]);
    return $this;
  }

  # Embed fields
  public function addField($name, $value=null, $inline=false) {
    $this->embed["fields"][] = is_array($name) ? $name : [
      "name" => $name,
      "value" => $value,
      "inline" => $inline
    ];
    return $this;
  }
  public function addFields(...$fields) {
    $this->_arrayAdderHandler("addField", $fields);
    return $this;
  }

  # Message files
  public function addFile($id, $path=null, $name=null, $type=null) {
    $snowflake = count($this->files);
    if (is_array($id)) {
      $this->files[$id["id"]] = [ 
        ...$id, 
        "snowflake" => $snowflake,
      ];
    } else {
      $this->files[$id] = [
        "path" => $path,
        "name" => $name,
        "type" => $type,
        "snowflake" => $snowflake,
      ];
    }
    return $this;
  }
  public function addFiles(...$files) {
    $this->_arrayAdderHandler("addFile", $files);
    return $this;
  }

  # Message attachments
  public function addAttachment($id, $filename=null) {
    $snowflake = $this->_getFileSnowflakeById($id);
    $this->attachments[] = is_array($id) ? $id : [
      "id" => $snowflake,
      "filename" => $filename,
    ];
    return $this;
  }
  public function addAttachments(...$attachments) {
    $this->_arrayAdderHandler("addAttachment", $attachments);
    return $this;
  }

  # Message flag
  public function setFlag($flag) {
    $this->flags = $flag;
    return $this;
  }

  # Message allowed mentions
  public function setAllowedMentions($data) {
    $this->allowedMentions = $data;
    return $this;
  }

  # Add Embed. Chain multiple embed
  // $embed = DiscordWebhook | DiscordEmbed
  public function addEmbed($embedClass, $indx=null, $replace=false) {
    // DiscordWebhook->embed | DiscordEmbed->toArray()
    $embed = property_exists($embedClass, "embed") ? $embedClass->embed : $embedClass->toArray();
    if (is_null($indx)) {
      // if embeds are still empty then assign this first embed to index 1, in respect to main embed,
      if (!$this->embeds && $this->embed) return $this->addEmbed($embedClass, 1);
      // otherwise append embed
      $this->embeds[] = $embed;
    } else {
      // if embed wants to be the first, change main embed offset to 1 to move it
      if ($indx == 0) $this->_offsetIndex = 1;
      // if indx is already taken and not replace, insert and move
      if (isset($this->embeds[$indx])) {
        array_splice($this->embeds, $indx-1, $replace ? 1 : 0, [$embed]);
      // insert embed based on index
      } else {
        $this->embeds[$indx] = $embed;
      }
    }
    return $this;
  }

  # Send Message
  public function send($opts=null) {

    $this->_setOpts($opts);

    if (!$this->webhook) throw new Exception("Webhook Required.");
    if (!$this->content && !$this->embed && !$this->embeds && !$this->files) throw new Exception("You must provide a value for at least one of content, embeds, or files.");

    $webhook = $this->webhook;

    $curlOpts = [
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
    ] + $this->curlOpts;

    $query = [];
    if ($this->wait) $query['wait'] = true;
    if ($this->threadId) $query['thread_id'] = $this->threadId;
    if ($query) $webhook .= '?' . http_build_query($query);

    if ($this->files) {
      $contentType = 'multipart/form-data';
      $data = $this->_getFormData();
    } else {
      $contentType = "application/json";
      $data = $this->_getData();
    }

    $curlOpts[CURLOPT_POSTFIELDS] = $data;
    $curlOpts[CURLOPT_HTTPHEADER][] = 'Content-type: '.$contentType;

    $ch = curl_init($webhook);

    curl_setopt_array($ch, $curlOpts);
    $res = curl_exec($ch);
    curl_close($ch);
    
    $curlErr = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $success = $code >= 200 && $code < 299;
    $body = $this->parseJSON ? $this->_silentJSONParse($res) : $res;
    
    return [
      'success' => $success,
      'body' => $body,
      'code' => $code,
      'curl_error' => $curlErr,
    ];

  }

}

?>
