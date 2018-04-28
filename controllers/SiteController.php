<?php

namespace app\controllers;

use app\helpers\parser\OlimpParser;
use app\helpers\parser\PariMatchParser;
use app\helpers\parser\ParserHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $olimpMatches = ParserHelper::parseOlimpLive();
        $pariMatchMatches = ParserHelper::parsePariMatch();

        return $this->render('index', [
            'olimpDP' => $this->generateDP($olimpMatches),
            'parMatchDP' => $this->generateDP($pariMatchMatches),
        ]);
    }

    public function actionWatch()
    {
        if (!($post = \Yii::$app->getRequest()->post())){
            return $this->redirect('index');
        }
        $olimp = ArrayHelper::getValue($post,'olimp');
        $parimatch = ArrayHelper::getValue($post,'parimatch');
        $olimpCoef = OlimpParser::getLiveOneItem($olimp);
        $parmatchCoef = PariMatchParser::getLiveOneItem($parimatch);

        return $this->render('watch',[
            'olimpCoef' => $olimpCoef,
            'parimatchCoef' => $parmatchCoef,
            'olimpMatchId' => $olimp,
            'parmiatchMatchId' => $parimatch
        ]);
    }

    public function actionUpdateCoefs()
    {
        if (!($get = \Yii::$app->getRequest()->get())){
            return $this->redirect('index');
        }
        $olimp = ArrayHelper::getValue($get,'olimp');
        $parimatch = ArrayHelper::getValue($get,'parimatch');
        $olimpCoef = OlimpParser::getLiveOneItem($olimp);
        $parmatchCoef = PariMatchParser::getLiveOneItem($parimatch);
        \Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return [
            'olimp' => $olimpCoef,
            'parimatch' => $parmatchCoef
        ];
    }

    /**
     * @param array $arr
     * @return ArrayDataProvider
     */
    private function generateDP(array $arr) {
        $dp = new ArrayDataProvider();
        $dp->setModels($arr);
        $dp->setSort(false);
        $dp->setPagination(false);
        return $dp;
    }
}
