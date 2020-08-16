<?php

class DiscordWebhook {
  
  # github.com/renzbobz
  # DiscordWebhook-PHP v.1
  # 8/16/20
  
  public $webhook;
  public $botIcon;
  public $botName;
  public $embeds = [];
  
  
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
      if (preg_match("/webhook/", $args[1])) {
        $webhook = $args[1];
      } else {
        $botIcon = $args[1];
      }
      
    } else {
      $arg = $args[0];
      $url = parse_url($arg);
      if (preg_match("/webhook/", $arg)) {
        $webhook = $arg;
      } else if ($url["host"]) {
        $botIcon = $arg;
      } else {
        $botName = $arg;
      }
      
    }
    
    $this->botName = $botName;
    $this->botIcon = $botIcon;
    $this->webhook = $webhook;
    
  }


  public function embed($Id) {
    
    $args = func_get_args();
    
    if ($Id[0] != "#") throw new Exception("UNABLE TO ADD EMBED: Your Embed Id must start with #. Like this > #$Id");
    
    if ($args[1] && $args[1] > 1) {
      for ($i = 0; $i < $args[1]; $i++) {
        $this->embeds[$Id][$i] = [];
      }
    } else {
      $this->embeds[$Id][0] = [];
      return $this->embeds[$Id][0];
    }
    
    return $this->embeds[$Id];
  }


  public function push($Id) {
    
    $args = func_get_args();
    
    if (!is_string($Id)) throw new Exception("UNABLE TO PUSH: You need to pass Embed Id.");
    
    if (empty($this->embeds[$Id])) throw new Exception("UNABLE TO PUSH: Embed Id not found.");
   
    $length = count($args);
    for ($i = 1; $i < $length; $i++) {
      $this->embeds[$Id][$i-1] = $args[$i];
    }
    
  }


  public function getData($Id) {
    
    if (!is_string($Id)) throw new Exception("UNABLE TO GET DATA: You need to pass Embed Id.");
   
    $embeds = $this->embeds[$Id];
    
    if (empty($embeds)) throw new Exception("UNABLE TO GET DATA: Embed Id not found.");
    
    $botIcon = $this->botIcon;
    $botName = $this->botName;
    $headers = [
      "Content-Type" => "application/json"
    ];
    
    $data = [];
    
    if ($botName) $data["username"] = $botName;
    if ($botIcon) $data["avatar_url"] = $botIcon;
    
    foreach ($embeds as $i => $embed) {
      if (empty($embed)) continue;
      if ($embed["content"]) $data["content"] = $embed["content"];
      if ($embed["tts"]) $data["tts"] = $embed["tts"];
      if ($embed["file"]) {
        $headers["Content-Type"] = "multipart/form-data";
        $headers["Content-Disposition"] = "form-data; filename=".time();
        $data["file"] = $embed["file"];
      }
      foreach ($embed as $k => $v) {
        if ($k == "embedId") continue;
        if ($k == "content") continue;
        if ($k == "tts") continue;
        if ($k == "file") continue;
        if ($data["file"]) {
          $data["payload_json"]["embeds"][$i][$k] = $v;
        } else {
          $data["embeds"][$i][$k] = $v;
        }
      }
    }
    
    $header = [];
    foreach ($headers as $k => $v) {
      $header[] = "$k: $v";
    }
    
    return [
      "data" => $data,
      "header" => $header
    ];
  }


  public function send($Id) {
    
    $args = func_get_args();
    $length = count($args);
    
    if ($Id[0] != "#") {
      $_id = "#_msg".rand(0,100);
      $e = $this->embed($_id);
      $e["content"] = $Id;
      $this->push($_id, $e);
      if ($length == 3) {
        return $this->send($_id, $args[1], $args[2]);
      } else if ($length == 2) {
        return $this->send($_id, $args[1]);
      } else {
        return $this->send($_id);
      }
    }
    
    if (!is_string($Id)) throw new Exception("UNABLE TO SEND: You need to pass Embed Id.");
    
    if (empty($this->embeds[$Id])) throw new Exception("UNABLE TO SEND: Embed Id not found.");
    
    $dataAry = $this->getData($Id);
    $header = $dataAry["header"];
    $data = $dataAry["data"];
    
    if (empty($data["embeds"]) && empty($data["content"])) throw new Exception("UNABLE TO SEND: You can't send an empty message!");
  
    if ($length == 3) {
      $webhook = $args[1];
      $callback = $args[2];
      
    } else if ($length == 2) {
      if (is_callable($args[1])) {
        $callback = $args[1];
        $webhook = $this->webhook;
      } else {
        $webhook = $args[1];
      }
      
    } else {
      $webhook = $this->webhook;
    }

    if (empty($webhook)) throw new Exception("UNABLE TO SEND: Webhook is not set.");
    
    $ch = curl_init($webhook);
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => $header,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    ]);
    $res = curl_exec($ch);
    $statusCode = curl_getinfo($ch)["http_code"];
    curl_close($ch);
    
    if ($statusCode == 204) {
      $success = true;
      $res = "Success !";
    } else {
      $success = false;
    }
    
    if (is_callable($callback)) $callback($success, $res, $statusCode);
    
    return [
      "success" => $success, 
      "response" => $res, 
      "statusCode" => $statusCode
    ];
  }


}

?>
