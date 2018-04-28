<?php
/**
 * Created by PhpStorm.
 * User: aligieri
 * Date: 4/10/18
 * Time: 11:06 AM
 */

namespace app\helpers\parser;


use yii\helpers\StringHelper;
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

    public static function getLiveOneItem($matchId)
    {
        $content = OlimpParser::getLiveWithCoef([$matchId]);
        $query = \phpQuery::newDocumentHTML($content);
        $tableRows = $query->find('table.totalizator tr');

        $matchName = '';
        $p1 = 0;
        $x = 0;
        $p2 = 0;
        $total = null;
        $under = null;
        $over = null;
        /** @var \DOMElement $tableRow */
        foreach ($tableRows->elements as $tableRow) {
            $pq = \phpQuery::pq($tableRow);
            if ($pq->hasClass('ishodRollHead')) {
                continue;
            } else {
                $rows = $pq->find('table.koeftable2 tr');

                foreach ($rows->elements as $element) {
                    $elementPQ = \phpQuery::pq($element);
                    if ($elementPQ->hasClass('hi')) {
                        $matchName = $elementPQ->find('td:last-child font.m')->text();
                    } else {
                        $coefsPanel = $elementPQ->find('td div.tab');
                        $coefsPanel->find('div[data-match-id-show]')->remove();

                        $nobr = $coefsPanel->find('nobr span.googleStatIssue');
                        /** @var \DOMElement $spans */
                        foreach ($nobr->elements as $spans) {
                            $span = \phpQuery::pq($spans);
                            $type = $span->find('span.googleStatIssueName')->text();
                            switch (strtoupper((string)$type)) {
                                case ParserHelper::FIRST_WINNER : {
                                    $p1 = $span->find('b.value_js')->text();
                                    break;
                                }
                                case ParserHelper::SECOND_WINNER: {
                                    $p2 = $span->find('b.value_js')->text();
                                    break;
                                }
                                case ParserHelper::NEUTRAL: {
                                    $x = $span->find('b.value_js')->text();
                                    break;
                                }
                                default : {
                                    $t = trim($type,' \t\n\r\0\x0B\-');
                                    $tot = 'TOT';
                                    // Starts With
                                    if (strtoupper(substr($t,0, strlen($tot))) === $tot) {
                                        $total = $t;
                                        $totals = $span->find('b.value_js')->texts();
                                        $under = $totals[0];
                                        $over = $totals[1];
                                    }

                                }
                            }
                        }
                    }
                }
            }
        };

        return [
            'name' => $matchName,
            'p1' =>$p1,
            'x' => $x,
            'p2' => $p2,
            'total' => $total,
            'over' => $over,
            'under' => $under,
        ];
    }

}