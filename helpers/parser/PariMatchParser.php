<?php
/**
 * Created by PhpStorm.
 * User: aligieri
 * Date: 4/12/18
 * Time: 3:12 PM
 */

namespace app\helpers\parser;


use yii\httpclient\Client;

class PariMatchParser extends BaseParser
{

    public static $uri = 'https://www.parimatch.kz';
    public static $liveActionURI = 'en/live_as.html?curs=0&curName=$&shed=0';

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

    public static function getLiveOneItem($matchId)
    {
        $o = new self();
        $req = $o->createRequest();
        
    }

    public function createRequest()
    {
        $req = parent::createRequest();
        $hdsCollection = $req->getHeaders()->add('X-Requested-With', 'XMLHttpRequest');
        return $req->setHeaders($hdsCollection);
    }

}