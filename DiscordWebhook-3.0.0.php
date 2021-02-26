<?php

class DiscordWebhook {
  
  
  # DiscordWebhook v.3.0
  # github.com/renzbobz
  # 8/18/20
  
  # Updated: 2/7/21
  
  
  public function __construct() {
   
    $args = func_get_args();
    
    if (empty($args)) return;
    
    $length = count($args);
    
    $webhook; $botName; $botIcon;
    
    switch ($length) {
      case 3: {
        
        $webhook = $args[2];
        $botIcon = $args[1];
        $botName = $args[0];
        
      }; break;
      case 2: {
        
        $botName = $args[0];
        if ($this->isDiscordWebhook($args[1])) {
          $webhook = $args[1];
        } else {
          $botIcon = $args[1];
        }
        
      }; break;
      case 1: {
        
        $arg = $args[0];
        $url = parse_url($arg);
        if ($this->isDiscordWebhook($args)) {
          $webhook = $arg;
        } else if ($url['host']) {
          $botIcon = $arg;
        } else {
          $botName = $arg;
        }
        
      }; break;
    }
    
    $this->botName = $botName;
    $this->botIcon = $botIcon;
    $this->webhook = $webhook;
    
  }
  
  public function __toString() {
    return json_encode($this->getData(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
  
  public function newEmbed() {
    return clone $this;
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
  
  # TITLE
  
  public function setTitle($title, $url='') {
    $this->title = $title;
    $this->url = $url;
    return $this;
  }
  public function appendTitle($title) {
    $this->title = $this->title.$title;
    return $this;
  }
  public function prependTitle($title) {
    $this->title = $title.$this->title;
    return $this;
  }
  
  # DESCRIPTION
  
  public function setDescription($desc) {
    $this->description = $desc;
    return $this;
  }
  public function appendDescription($desc) {
    $this->description = $this->description.$desc;
    return $this;
  }
  public function prependDescription($desc) {
    $this->description = $desc.$this->description;
    return $this;
  }
  
  # COLOR
  
  public function setColor($color) {
    $this->color = $color;
    return $this;
  }
  
  # TIMESTAMP 
  
  public function setTimestamp($time) {
    $this->timestamp = $time;
    return $this;
  }
  
  # AUTHOR
  
  public function setAuthor($name, $url='', $icon='') {
    $this->author = [
      'name' => $name,
      'url' => $url,
      'icon_url' => $icon
    ];
    return $this;
  }
  
  # THUMBNAIL

  public function setThumbnail($url, $height=0, $width=0) {
    $this->thumbnail = [
      'url' => $url,
      'height' => $height,
      'width' => $width
    ];
    return $this;
  }
  
  # IMAGE
  
  public function setImage($url, $height=0, $width=0) {
    $this->image = [
      'url' => $url,
      'height' => $height,
      'width' => $width
    ];
    return $this;
  }
  
  # FOOTER

  public function setFooter($text, $icon='') {
    $this->footer = [
      'text' => $text,
      'icon_url' => $icon
    ];
    return $this;
  }
  
  # FIELD

  public function addField($name, $val, $inline=false) {
    $this->fields[] = [
      'name' => $name,
      'value' => $val,
      'inline' => $inline
    ]; 
    return $this;
  }
  
  public function getData($args=[]) {
    
    $length = count($args);
    $objs = get_object_vars($this);
    
    $botIcon = $objs['botIcon'];
    $botName = $objs['botName'];
    $content = $objs['content'];
    
    $data = [];
    $nm = true;
    
    if ($content) $data['content'] = $content;
    if ($botName) $data['username'] = $botName;
    if ($botIcon) $data['avatar_url'] = $botIcon;
   
    switch ($length) {
      case 2: {
        
        $nm = false;
        $data['content'] = $args[0];
        $this->webhook = $args[1];
        
      }; break;
      case 1: {
        
        if ($this->isDiscordWebhook($args[0])) {
          $this->webhook = $args[0];
        } else {
          $nm = false;
          $data['content'] = $args[0];
        }
        
      }; break;
    }
    
    if ($nm) {
      foreach ($objs as $k => $v) {
        $exs = ['botName', 'botIcon', 'webhook', 'content'];
        if (in_array($k, $exs)) continue;
        $data['embeds'][0][$k] = $v;
      }
    }
    
    return $data;
    
  }
  
  public function isDiscordWebhook($url) {
    $regex = '/(discord.com|discordapp.com)\/api\/webhooks\/(\d+)\/(.*)/';
    return preg_match($regex, $url);
  }
  
  private function _send($webhook, $data) {
    
    if (!$data['content'] && !$data['embeds']) throw new Exception('UNABLE TO SEND: Empty message.');
    
    if (!$webhook) throw new Exception('UNABLE TO SEND: Webhook is not set.');

    $ch = curl_init($webhook);
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => ['Content-type: application/json'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    ]);
    $res = curl_exec($ch);
    $statusCode = curl_getinfo($ch)['http_code'];
    curl_close($ch);
    
    $success = $statusCode == 204;
    
    return [
      'success' => $success, 
      'response' => $res, 
      'statusCode' => $statusCode
    ];
    
  }
  
  
  public function sendMultiEmbed() {
    
    $args = func_get_args();
    $length = count($args);
    
    if (!$length) return false;
    
    $webhook = is_object($args[0]) ? $this->webhook : $args[0];
    
    $embeds = [];
    foreach ($args as $arg) {
      if (!is_object($arg)) continue;
      $data = $arg->getData();
      $content = $data["content"];
      $embeds[] = $data["embeds"][0];
    }
    
    $data = [
      "content" => $content,
      "username" => $this->botName,
      "avatar_url" => $this->botIcon,
      "embeds" => $embeds
    ];
    
    return $this->_send($webhook, $data);
    
  }
  
  
  public function send() {
    
    $args = func_get_args();
    $data = $this->getData($args);
    $webhook = $this->webhook;
    
    return $this->_send($webhook, $data);
    
  }
  
  
}


?>
