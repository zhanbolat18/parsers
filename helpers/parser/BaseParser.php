<?php
/**
 * Created by PhpStorm.
 * User: aligieri
 * Date: 4/12/18
 * Time: 3:28 PM
 */

namespace app\helpers\parser;


use yii\httpclient\Client;

abstract class BaseParser implements ParserInterface
{
    public static $uri;
    public static $liveActionURI;


    protected $client;
    protected $body;

    public function __construct()
    {
        $this->client = new Client(['baseUrl' => static::$uri]);
    }


    /**
     * @return \yii\httpclient\Request
     */
    public function createRequest()
    {
        $req = $this->client->createRequest();
        $req->setHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-language' => 'en-US,en;q=0.5',
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0',
        ]);
        return $req;
    }


    public function getLiveContent()
    {
        $req = $this->createRequest();
        $req->setMethod('GET');
        $req->setUrl(static::$liveActionURI);
        $res = $req->send();
        if (!$res->getIsOk()){
            throw new \HttpRequestException($res->getContent());
        }
        $contentType = $res->getHeaders()->get('Content-type');
        if (($p = strpos($contentType, 'charset')) !== false) {
            $charset = substr(rtrim($contentType),$p + strlen('charset='));
            if (!in_array(strtolower($charset),['utf-8', 'utf8'])) {
                return mb_convert_encoding($res->getContent(),'utf-8',$charset);
            }
        }
        return $res->getContent();
    }
}