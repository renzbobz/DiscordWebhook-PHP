<?php

class DiscordWebhook {
  
  
  # DiscordWebhook-PHP
  # github.com/renzbobz
  # 3/18/21
  

  public function __construct($opts=[]) {
    
    $this->embeds = [];
    $this->wait_message = true;
    
    if (empty($opts)) return;
    
    if (is_array($opts)) {
      $this->setOpts($opts);
    } else {
      $this->setWebhook($opts);
    }
    
  }
  
  public function __toString() {
    return $this->toJSON();
  }
  
  public function toArray() {
    return (array) $this;
  }
  
  public function toJSON() {
    return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
  
  public function setUsername($username) {
    $this->username = $username;
    return $this;
  }
  
  public function setAvatar($avatar) {
    $this->avatar_url = $this->resolveURL($avatar);
    return $this;
  }
  
  public function setWebhook($webhook) {
    $this->webhook = $webhook;
    return $this;
  }
  
  private function setOpts($opts) {
    if (isset($opts["username"])) $this->setUsername($opts["username"]);
    if (isset($opts["avatar"])) $this->setAvatar($opts["avatar"]);
    if (isset($opts["webhook"])) $this->setWebhook($opts["webhook"]);
    if (isset($opts["wait_message"])) $this->wait_message = $opts["wait_message"];
  }
  
  private function set($key, $val) {
    $this->embeds[0][$key] = $val;
  }
  
  private function get($key) {
    return $this->embeds[0][$key];
  }
  
  private function getBaseURL() {
    $scheme = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https" : "http";
    $host = $_SERVER["HTTP_HOST"];
    $url = $scheme."://".$host;
    return $url;
  }
  
  private function resolveColor($color) {
    if ($color) {
      if (is_string($color)) {
        if ($color == "RANDOM") $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        if (preg_match("/,/", $color)) $color = sprintf("#%02x%02x%02x", ...explode(",", $color));
        $color = hexdec($color);
      }
    }
    return $color;
  }
  
  private function resolveURL($url) {
    if (!preg_match("/(http|https)\:\/\//", $url)) {
      $self = $_SERVER["PHP_SELF"];
      $selfDir = dirname($self);
      $selfDirArr = explode("/", $selfDir);
      $filePath = realpath($url);
      $fpArr = explode("/", $filePath);
      $fpArrLength = count($fpArr);
      foreach ($fpArr as $indx => $val) {
        if (!$val) continue;
        if (in_array($val, $selfDirArr)) {
          array_splice($fpArr, 0, $indx);
          $url = implode("/", $fpArr);
          break;
        } else {
          if ($fpArrLength - 1 == $indx) $url = $val;
        }
      }
      $url = $this->getBaseURL()."/".$url;
    }
    return $url;
  }
  
  public function newMessage($message='') {
    $cloned = clone $this;
    if ($message) $cloned->setContent($message);
    return $cloned;
  }
  
  public function isDiscordWebhook($url) {
    $regex = '/(discord.com|discordapp.com)\/api\/webhooks\/(\d+)\/(.*)/';
    return preg_match($regex, $url);
  }
  
  public function insertTo($embedObj, $index=null) {
    $embeds = $this->embeds;
    foreach ($embeds as $indx => $embed) {
      if (isset($index)) {
        array_splice($embedObj->embeds, $index+$indx, 0, [$embed]);
      } else {
        $embedObj->embeds[] = $embed;
      }
    }
    return $this;
  }
  
  # CONTENT
  
  public function setContent($content) {
    $this->content = $content;
    return $this;
  }
  public function appendContent($content) {
    $this->content = $this->content.$content;
    return $this;
  }
  public function prependContent($content) {
    $this->content = $content.$this->content;
    return $this;
  }
  
  # TEXT-TO-SPEECH
  
  public function setTts($tts=false) {
    $this->tts = $tts;
    return $this;
  }
  
  # TITLE
  
  public function setTitle($title, $url='') {
    $this->set("title", $title);
    if ($url) $this->setURL($url);
    return $this;
  }
  public function appendTitle($title) {
    $this->set("title", $this->get("title").$title);
    return $this;
  }
  public function prependTitle($title) {
    $this->set("title", $title.$this->get("title"));
    return $this;
  }
  
  # URL 
  
  public function setURL($url) {
    $this->set("url", $this->resolveURL($url));
    return $this;
  }
  
  # DESCRIPTION
  
  public function setDescription($desc) {
    $this->set("description", $desc);
    return $this;
  }
  public function appendDescription($desc) {
    $this->set("description", $this->get("description").$desc);
    return $this;
  }
  public function prependDescription($desc) {
    $this->set("description", $desc.$this->get("description"));
    return $this;
  }
  
  # COLOR
  
  public function setColor($color=0) {
    $this->set("color", $this->resolveColor($color));
    return $this;
  }
  
  # TIMESTAMP 
  
  public function setTimestamp($timestamp=0) {
    if (!$timestamp) $timestamp = date('c');
    $this->set("timestamp", $timestamp);
    return $this;
  }
  
  # AUTHOR
  
  public function setAuthor($name, $url='', $icon='') {
    $this->set("author", [
      'name' => $name,
      'url' => $url ? $this->resolveURL($url) : $url,
      'icon_url' => $icon ? $this->resolveURL($icon) : $icon
    ]);
    return $this;
  }
  
  # THUMBNAIL

  public function setThumbnail($url, $height=0, $width=0) {
    $this->set("thumbnail", [
      'url' => $this->resolveURL($url),
      'height' => $height,
      'width' => $width
    ]);
    return $this;
  }
  
  # IMAGE
  
  public function setImage($url, $height=0, $width=0) {
    $this->set("image", [
      'url' => $this->resolveURL($url),
      'height' => $height,
      'width' => $width
    ]);
    return $this;
  }
  
  # FOOTER

  public function setFooter($text, $icon='') {
    $this->set("footer", [
      'text' => $text,
      'icon_url' => $icon ? $this->resolveURL($icon) : $icon
    ]);
    return $this;
  }
  
  # FIELDS
  
  public function addField($name, $val, $inline=false, $index=null) {
    $field = [$name, $val, $inline];
    if (isset($index)) {
      $this->spliceFields($index, 0, $field);
    } else {
      $this->embeds[0]["fields"][] = $this->formatField(...$field);
    }
    return $this;
  }
  
  private function formatField($name, $val, $inline=false) {
    return [
      'name' => $name,
      'value' => $val,
      'inline' => $inline
    ];
  }
  
  public function addFields(...$fields) {
    foreach ($fields as $field) {
      if (empty($field)) continue;
      $this->addField(...$field);
    }
    return $this;
  }
  
  public function spliceFields($index, $deleteCount=0, ...$fields) {
    if (!empty($fields)) {
      $fields = array_map(function($field) {
        return $this->formatField(...$field);
      }, $fields);
    }
    array_splice($this->embeds[0]["fields"], $index, $deleteCount, $fields);
    return $this;
  }
  
  # FILES 
  
  public function addFile($file, $name='') {
    if (!file_exists($file)) throw new Exception("FILE DOESN'T EXIST! : ".$file);
    $this->files[] = $this->formatFile($file, $name);
    return $this;
  }
  
  public function addFiles(...$files) {
    foreach ($files as $file) {
      $this->addFile(...$file);
    }
    return $this;
  }
  
  private function formatFile($file, $name='') {
    return [
      "file" => $file,
      "name" => $name
    ];
  }
  
  public function spliceFiles($index, $deleteCount=0, ...$files) {
    if (!empty($files)) {
      $files = array_map(function($file) {
         return $this->formatFile(...$file);
      }, $files);
    }
    array_splice($this->files, $index, $deleteCount, $files);
    return $this;
  }
  
  
  # SEND
  
  
  public function send($opts=[]) {
    
    $webhook = $this->webhook;
    
    if ($opts) {
      if (is_array($opts)) {
        $this->setOpts($opts);
      } else {
        $webhook = $opts;
      }
    }
    
    if (!isset($webhook) || !$this->isDiscordWebhook($webhook)) throw new Exception('UNABLE TO SEND MESSAGE WITHOUT WEBHOOK OR INVALID WEBHOOK!');
    
    if (!isset($this->content) && empty($this->embeds)) throw new Exception('UNABLE TO SEND AN EMPTY MESSAGE.');
    
    if ($this->wait_message) $webhook = $webhook . "?wait=true";
    
    $curlopts = [
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true
    ];
    
    if (isset($this->files)) {
      $contentType = "multipart/form-data";
      foreach ($this->files as $i => $file) $this->{'file_'.++$i} = curl_file_create($file["file"], null, $file["name"]);
      unset($this->files);
      $data = $this->toArray() + [
        "payload_json" => $this->toJSON()
      ];
    } else {
      $contentType = "application/json";
      $data = $this->toJSON();
    }
    
    $curlopts[CURLOPT_POSTFIELDS] = $data;
    $curlopts[CURLOPT_HTTPHEADER][] = 'Content-type: '.$contentType;
    
    $ch = curl_init($webhook);
    curl_setopt_array($ch, $curlopts);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $success = $code >= 200 && $code < 299;
    
    return (object) [
      'success' => $success, 
      'body' => $res,
      'code' => $code,
      'message' => $success ? json_decode($res) : null
    ];
    
  }
  
  
}

?>
