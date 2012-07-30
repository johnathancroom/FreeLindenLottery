<?php

/* Curl request */
function curl($url) {
  $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //Doesn't work in safe mode (MediaTemple).
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  return curl_exec($ch);
}

/* Get profile pic */
function getProfileUrl($username, $full = false) {
  $url = "https://my-secondlife.s3.amazonaws.com/users/".strtolower($username)."/".($full ? "" : "thumb_")."sl_image.png";
  if(curl($url) !== false)
  {
    return $url;
  }
  else
  {
    return "/static4/images/avatar.jpg";
  }
}