<?php

class DiscordWebhook {
  
  
  # DiscordWebhook (New)
  # github.com/renzbobz
  # 8/18/20
  
  # Updated: 12/03/20
  
  
  public function __construct() {
   
    $args = func_get_args();
    
    if (empty($args)) return;
    
    $length = count($args);
    
    $webhook; $botName; $botIcon;
    
    if ($length == 3) {
      $webhook = $args[2];
      $botIcon = $args[1];
      $botName = $args[0];
      
    } else if ($length == 2) {
      $botName = $args[0];
      if (preg_match('/webhook/', $args[1])) {
        $webhook = $args[1];
      } else {
        $botIcon = $args[1];
      }
      
    } else {
      $arg = $args[0];
      $url = parse_url($arg);
      if (preg_match('/webhook/', $arg)) {
        $webhook = $arg;
      } else if ($url['host']) {
        $botIcon = $arg;
      } else {
        $botName = $arg;
      }
      
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
  
  public function setContent($content) {
    $this->content = $content;
    return $this;
  }
  
  public function setTitle($title, $url='') {
    $this->title = $title;
    $this->url = $url;
    return $this;
  }
  
  public function setDescription($desc) {
    $this->description = $desc;
    return $this;
  }
  
  public function setColor($color) {
    $this->color = $color;
    return $this;
  }
  
  public function setTimestamp($time) {
    $this->timestamp = $time;
    return $this;
  }
  
  public function setAuthor($name, $url='', $icon='') {
    $this->author = [
      'name' => $name,
      'url' => $url,
      'icon_url' => $icon
    ];
    return $this;
  }

  public function setThumbnail($url) {
    $this->thumbnail = [
      'url' => $url
    ];
    return $this;
  }
  
  public function setImage($url) {
    $this->image = [
      'url' => $url
    ];
    return $this;
  }

  public function setFooter($text, $icon='') {
    $this->footer = [
      'text' => $text,
      'icon_url' => $icon
    ];
    return $this;
  }

  public function addField($name, $val, $inline=false) {
    $this->fields[] = [
      'name' => $name,
      'value' => $val,
      'inline' => $inline
    ]; 
    return $this;
  }
  
  public function getData() {
    
    $args = func_get_args();
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
   
    if ($length == 2) {
      $nm = false;
      $data['content'] = $args[0];
      $this->webhook = $args[1];
      
    } else if ($length == 1) {
      if (preg_match('/webhooks/', $args[0])) {
        $webhook = $args[0];
      } else {
        $nm = false;
        $data['content'] = $args[0];
      }
    }
    
    if ($nm) 
      foreach ($objs as $k => $v) {
        if ($k == 'botName') continue;
        if ($k == 'botIcon') continue;
        if ($k == 'webhook') continue;
        if ($k == 'content') continue;
        $data['embeds'][0][$k] = $v;
      }
    
    
    return $data;
    
  }
  
  public function send() {
    
    $data = $this->getData();
    $webhook = $this->webhook;
    
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
  
  
}


?>
