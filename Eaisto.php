<?php
namespace engine\parse;

use engine\parse\BaseParser;
use engine\utils\anticaptcha\CaptchaSolver;

class Eaisto extends BaseParser
{
    public function __call($func, $params)
    {
        $vin = $params[0];
        $regNum = $params[1];
        $this->initHTTP();
        $this->http->headers = [
            "User-Agent"=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 ' .
            '(KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
        ];
        $captcha = $this->http->fetch("https://eaisto.info/securimage_show.php");
        $rawHeaders = $this->http->getHeaders();
        $headers  = [
            "Cookie" => preg_replace("/([^=]+=[^;]+).*/","$1",isset($rawHeaders["Set-Cookie"])?$rawHeaders["Set-Cookie"]:""),
            "origin: https://eaisto.info",
            "referer: https://eaisto.info/"
        ];
        $this->http->headers = $headers;
        $captchaSolver = new CaptchaSolver(); 
        $word = $captchaSolver->getCaptchaWord($captcha);
        if ($word === false) {
          return "";  
        } 
        $res = $this->http->fetch("https://eaisto.info/","POST",[
            "vin" => $vin,
            "captcha_code" => $word,
            "action"=> "checkNum",
            "registr"=> $regNum,
            "body"=> "",
            "chassis"=> "",
            "eaisto"=> ""
        ]);
        $this->http->close();
        return $res;
    }
};
?>
