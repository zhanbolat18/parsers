<?php

namespace app\helpers\parser;


class ParserHelper
{
    const FIRST_WINNER = '1';
    const SECOND_WINNER = '2';
    const NEUTRAL = 'X';
    const FIRST_OR_NEUTRAL = '1X';
    const NEUTRAL_OR_SECOND = 'X2';
    const NO_NEUTRAL = '12';




    public static $httpClient = null;


    public static function parseOlimpLive()
    {
        $content = OlimpParser::getBodyToParse();
        $query = \phpQuery::newDocumentHTML($content);
        $liveIds = [];
        $query
            ->find('table.live_main_table tr[data-kek="row"] td:first-child input')
            ->each(function(\DOMElement $item) use(&$liveIds) {
                $id = trim($item->getAttribute('value'));
                if ($id){
                    $liveIds[] = $id;
                }
            });
        $content = OlimpParser::getLiveWithCoef($liveIds);
        $query = \phpQuery::newDocumentHTML($content);
        $tableRows = $query->find('table.totalizator tr');

        $allModels = [];
        $currentSportType = '';

        /** @var \DOMElement $tableRow */
        foreach ($tableRows->elements as $tableRow) {
            $pq = \phpQuery::pq($tableRow);
            if ($pq->hasClass('ishodRollHead')) {
                $panelHeader = $pq->children('td:first-child')->text();
                $headers = array_map('trim',explode('.',$panelHeader));
                $currentSportType = $headers[1];
                $allModels[$currentSportType]['sportType'] = $headers[1];
            } else {
                $rows = $pq->find('table.koeftable2 tr');
                $matchName = '';
                foreach ($rows->elements as $element) {
                    $elementPQ = \phpQuery::pq($element);
                    if ($elementPQ->hasClass('hi')) {
                        $matchName = $elementPQ->find('td:last-child font.m')->text();
                        $allModels[$currentSportType]['matches'][$matchName]['name'] = $matchName;
                    } else {
                        $coefsPanel = $elementPQ->find('td div.tab');
                        $allModels[$currentSportType]['matches'][$matchName]['matchId'] = $coefsPanel->attr('data-match-id');
                        $coefsPanel->find('div[data-match-id-show]')->remove();
//                            $allModels[$currentSportType]['matches'][$j]['coef'] = $coefsPanel->text();

                        $nobr = $coefsPanel->find('nobr span.googleStatIssue');
                        /** @var \DOMElement $spans */
                        foreach ($nobr->elements as $spans) {
                            $span = \phpQuery::pq($spans);
                            $type = $span->find('span.googleStatIssueName')->text();
                            if (
                                in_array( strtoupper((string)$type),[
                                        self::FIRST_WINNER, self::SECOND_WINNER, self::NEUTRAL,
                                        self::FIRST_OR_NEUTRAL, self::NEUTRAL_OR_SECOND, self::NO_NEUTRAL
                                    ]
                                )
                            ){
                                $allModels[$currentSportType]['matches'][$matchName]['coefs'][] = [
                                    'coefType' => $type,
                                    'coefValue' => $span->find('b.value_js')->text(),
                                ];
                            }
                        }
                    }
                }
            }
        };
        return $allModels;

    }

    public static function parsePariMatch()
    {
        $content = PariMatchParser::getBodyToParse();
        $query = \phpQuery::newDocumentHTML( $content);

        $mainContent = $query->find('div.wrapper');
        $sports = $mainContent->find('div.sport');

        $allMatches = [];
        $sports->each(function (\DOMElement $item) use(&$allMatches) {
            $pq = \phpQuery::pq($item);
            $sportType = $pq->children('p.sport')->find('a')->text();
            $tables = $pq->find('div.item div.subitem table.dt');
            $match['sportType'] = $sportType;
            /** @var \DOMElement $table */
            foreach ( $tables as $table) {
                $tbody = \phpQuery::pq($table)->find('tbody');
                $game = $tbody->find('td.td_n')->text();

                /** @var \DOMElement[] $coefs */
                $coefs = $tbody->find('td')->elements;
                $tr = array_shift($coefs);
                $matchId = $tr->getElementsByTagName('input')->item(0)->getAttribute('value');
                array_shift($coefs);
                $firstWin = array_shift($coefs);
                $neutral = array_shift($coefs);
                $secondWin = array_shift($coefs);

                $match['matches'][] = [
                    'name' => $game,
                    'matchId' => $matchId,
                    'coefs' => [
                        ["coefType" => self::FIRST_WINNER, "coefValue" => $firstWin->textContent ],
                        ["coefType" => self::NEUTRAL, "coefValue" => $neutral->textContent ],
                        ["coefType" => self::SECOND_WINNER, "coefValue" => $secondWin->textContent, ],
                    ],
                ];
            }
            $allMatches[] = $match;
        });
        return $allMatches;
    }




    private function tmp()
    {
        $content = OlimpParser::getBodyToParse();
        $query = \phpQuery::newDocumentHTML($content);
        $table = $query->find('table.live_main_table');
        $matchTypes = $table->find('tr > td[data-kek="arrow"]');
        $matches = $table->find('tr[data-kek="row"]');
        $allMatches = [];
        $matchTypes->each(function (\DOMElement $item) use ($matches, &$allMatches){
            $filterIns = $matches->newInstance();
            $sportId = $item->getAttribute('data-sport');
            $filterIns->filterCallback(function ($idx, \DOMElement $match) use ($sportId){
                $matchBelongsTo = $match->getAttribute("data-sport");
                return (int)$matchBelongsTo === (int)$sportId;
            }, true);

            $map = array_reduce($filterIns->elements,function ($carr, \DOMElement $item){
                $nodeList = $item->getElementsByTagName('a');
                for ($i = 0; $i < $nodeList->length; $i++){
                    $a = $nodeList->item($i);
                    $value = trim($a->nodeValue);
                    if ($value) {
                        $carr[] = [
                            'match' => $value,
                            'link' => $a->getAttribute('href'),
                        ];
                        break;
                    }
                }
                return $carr;
            }, []);

            $allMatches[] = [
                'sportType' => $item->getElementsByTagName('b')->item(0)->nodeValue,
                'matches' => $map
            ];
        });
        return $allMatches;
    }

}