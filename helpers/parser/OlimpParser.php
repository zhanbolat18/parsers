<?php
/**
 * Created by PhpStorm.
 * User: aligieri
 * Date: 4/10/18
 * Time: 11:06 AM
 */

namespace app\helpers\parser;


use yii\httpclient\Client;

class OlimpParser extends BaseParser
{

    public static $uri = 'https://olimp.com';
    public static $liveActionURI = 'betting';

    public function getBody()
    {
        if ($this->body === null) {
            $this->body = $this->getLiveContent();
        }
        return $this->body;
    }

    public static function getBodyToParse()
    {
        return (new self())->getBody();
    }

    public static function getLiveWithCoef(array $ids)
    {
        $getUri = 'index.php?page=line&action=2&currpage=live&time=0';
        $getUri .= '&live[]=' . implode('&live[]=',$ids);
        $obj = new self();
        $request = $obj->createRequest();
        $request->setMethod('GET')->setUrl($getUri);
        $request->headers->add('X-Requested-With', 'XMLHttpRequest');
        $resp = $request->send();

        if ( !$resp->getIsOk() ) {
            throw new \HttpRequestException($resp->statusCode);
        }
        return $resp->getContent();
    }

}