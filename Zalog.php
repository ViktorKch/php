<?php
namespace engine\parse;

use engine\parse\BaseParser;
use engine\utils\anticaptcha\CaptchaSolver;

class Zalog extends BaseParser
{
    public function get($vin)
    {   
        $this->initHTTP();

        $res = '{}';

        try{
            $captchaSolver = new CaptchaSolver();

            $html = $this->http->fetch('https://www.reestr-zalogov.ru/search/index');
            $uuid = 'b6fa0009-2777-461c-94b1-7482368990dc';

            if (preg_match("/id=\"uuid\"\s+name=\"uuid\"\s+value=\"([^\"]+)\"/im", $html, $m)) {
                $uuid = $m[1];
            }

            $captcha = $this->http->fetch('https://www.reestr-zalogov.ru/captcha/generate?' . $this->random());
            $word = $captchaSolver->getCaptchaWord($captcha);

            if ($word === false) {
                return $res;
            } 

            $request = [
                'VIN' => $vin,
                'token' => $word,
                'formName' => 'vehicle-form',
                'uuid' => $uuid
            ];

            $res = $this->http->fetch('https://www.reestr-zalogov.ru/search/endpoint', 'POST' , $request);
            $this->http->close();
        }
        catch(\Exception $e){
            return false;
        }

        return $res;
    }

    protected function random() 
    {
        return round(microtime(true) * 1000)."";
    }
};
?>
