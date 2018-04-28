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
        $content = self::getOneItemContent($matchId);
        $phpQuery = \phpQuery::newDocumentHTML($content);
        $table = $phpQuery->find('table.dt.twp');
        $ths = $table->find('tr:first th');
        $indexes = [];
        foreach ($ths->elements as $key => $th){
            $tc = strtoupper( trim( (string)$th->textContent ) );
            if ( in_array(
                    $tc,
                    ['1','2','X','TOTAL','OVER','UNDER']
                )
            ){
                $indexes[$key] = $tc;
            }
        }
        $trow = $table->find('tbody.row1:first tr.bk');
        $matchName = $trow->find('td.l')->text();
        /** @var \DOMElement[] $tds */
        $tds = $trow->find('td')->elements;
        $p1 = null;
        $x = null;
        $p2 = null;
        $total = null;
        $over = null; $under = null;
        $counter = 0;
        foreach($tds as $td){
            $cs = $td->getAttribute('colspan');
            if ($cs) {
                $counter += (int)$cs;
                continue;
            }
            if (isset($indexes[$counter])){
                switch ($indexes[$counter]){
                    case '1':{
                        $p1 = $td;
                        break;
                    }
                    case '2':{
                        $p2 = $td;
                        break;
                    }
                    case 'X':{
                        $x = $td;
                        break;
                    }
                    case 'TOTAL':{
                        $total = $td;
                        break;
                    }
                    case 'OVER': {
                        $over = $td;
                        break;
                    }
                    case 'UNDER' : {
                        $under = $td;
                    }

                }
            }
            $counter += 1;
        }

        return [
            'name' => $matchName,
            'p1' => $p1 ? $p1->textContent : null,
            'x' => $x ? $x->textContent : null,
            'p2' => $p2 ? $p2->textContent : null,
            'total' => $total ? $total->textContent : null,
            'over' => $over ? $over->textContent : null,
            'under' => $under ? $under->textContent : null,
        ];
    }

    public function createRequest()
    {
        $req = parent::createRequest();
        $hdsCollection = $req->getHeaders()->add('X-Requested-With', 'XMLHttpRequest');
        return $req->setHeaders($hdsCollection);
    }

    protected static function getOneItemContent($matchId)
    {
        $o = new self();
        $req = $o->createRequest();
        $req->setMethod('GET');
        $req->setUrl('en/live_ar.html?hl=' . $matchId . '&he=' . $matchId . '&curs=0&curName=$');
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