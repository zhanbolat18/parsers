<?php

namespace app\controllers;

use app\helpers\parser\ParserHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\Controller;

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

        var_dump($post); die;
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
