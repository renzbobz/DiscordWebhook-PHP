<?php

class DiscordWebhook {
  
  
  # DiscordWebhook v.3.5
  # github.com/renzbobz
  # 8/18/20
  
  # Updated: 3/9/21
  
  
  public function __construct() {
   
    $args = func_get_args();
    
    if (empty($args)) return;
    
    $this->bot = new stdClass;
    
    $length = count($args);
    
    switch ($length) {
      case 3: {
        
        $webhook = $args[2];
        $botAvatar = $args[1];
        $botUsername = $args[0];
        
      }; break;
      case 2: {
        
        $botUsername = $args[0];
        if ($this->isDiscordWebhook($args[1])) {
          $webhook = $args[1];
        } else {
          $botAvatar = $args[1];
        }
        
      }; break;
      case 1: {
        
        $arg = $args[0];
        $url = parse_url($arg);
        if ($this->isDiscordWebhook($arg)) {
          $webhook = $arg;
        } else if ($url['host']) {
          $botAvatar = $arg;
        } else {
          $botUsername = $arg;
        }
        
      }; break;
    }
    
    $this->setUsername($botUsername);
    $this->setAvatar($botAvatar);
    $this->setWebhook($webhook);
    
  }
  
  public function setUsername($username) {
    $this->bot->username = $username;
  }
  
  public function setAvatar($avatar) {
    $this->bot->avatar = $avatar;
  }
  
  public function setWebhook($webhook) {
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
  
  public function setColor($color=0) {
    $this->color = $color;
    return $this;
  }
  
  # TIMESTAMP 
  
  public function setTimestamp($time=0) {
    if (!$time) $time = date('c');
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

  public function addField($name, $val, $inline=false, $index=null) {
    $field = [
      'name' => $name,
      'value' => $val,
      'inline' => $inline
    ];
    if (isset($index)) {
      array_splice($this->fields, $index, 0, [$field]);
    } else {
      $this->fields[] = $field;
    }
    return $this;
  }
  
  # TEXT-TO-SPEECH
  
  public function setTts($tts=false) {
    $this->tts = $tts;
    return $this;
  }
  
  
  public function getData($args=[]) {
    
    $length = count($args);
    $objs = get_object_vars($this);
    
    $tts = $objs['tts'];
    $bot = $objs['bot'];
    $content = $objs['content'];
    
    if ($bot) {
      $botAvatar = $bot->avatar;
      $botUsername = $bot->username;
    }
    
    $data = [];
    $nm = false;
    
    if ($tts) $data['tts'] = $tts;
    if ($content) $data['content'] = $content;
    if ($botUsername) $data['username'] = $botUsername;
    if ($botAvatar) $data['avatar_url'] = $botAvatar;
   
    switch ($length) {
      case 3: {
        
        $data['content'] = $args[0];
        $this->webhook = $args[1];
        $data['tts'] = $args[2];
        
      }; break;
      case 2: {
        
        $data['content'] = $args[0];
        if ($this->isDiscordWebhook($args[1])) {
          $this->webhook = $args[1];
        } else {
          $data['tts'] = $args[1];
        }
        
      }; break;
      case 1: {
        
        if ($this->isDiscordWebhook($args[0])) {
          $nm = true;
          $this->webhook = $args[0];
        } else {
          $data['content'] = $args[0];
        }
        
      }; break;
    }
    
    if ($nm || !$length) {
      foreach ($objs as $k => $v) {
        $exs = ['bot', 'webhook', 'content', 'tts'];
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
    
    if (!$webhook || !$this->isDiscordWebhook($webhook)) throw new Exception('UNABLE TO SEND: Webhook is not set.');

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
    
    $bot = $this->bot;
    $data = [
      "content" => $content,
      "username" => $bot->username,
      "avatar_url" => $bot->avatar,
      "tts" => $this->tts,
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