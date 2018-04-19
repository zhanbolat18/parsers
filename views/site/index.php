<?php
/* @var $olimpDP \yii\data\ArrayDataProvider */
/* @var $parMatchDP \yii\data\ArrayDataProvider */

use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$this->title = 'Parser';
?>
<div class="site-index">

    <div class="body-content">
        <?php $activeForm = \yii\widgets\ActiveForm::begin(['action' => '/site/watch']) ?>
        <div class="row"><?= \yii\helpers\Html::submitButton('Submit',['class' => 'btn btn-primary']) ?></div>
        <div class="row">
            <div class="col-lg-6 col-md-6">
            <?= GridView::widget([
                'dataProvider' => $olimpDP,
                'columns' => [
                    'sportType',
                    [
                        'attribute' => 'matches',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return array_reduce($model['matches'], function ($carr, $item){
                                $coefs = array_reduce( ArrayHelper::getValue($item,'coefs',[]) , function ($r, $i){
                                    return $r . "<div class='col-lg-2'><b>{$i['coefType']}</b> {$i['coefValue']}</div> ";
                                },'');
                                return $carr . "
                                        <div class='panel panel-default'>
                                            <div class='panel-body'>
                                                 <div class='row'>
                                                 <label>
                                                    <input type='radio' name='olimp' value='{$item['matchId']}'>
                                                    <a data-match-id='{$item['matchId']}'>{$item['name']}</a>
                                                    
                                                </label>     
                                                </div>
                                                <div class='row'>
                                                    {$coefs}
                                                </div>
                                            </div>
                                        </div>
                                    
                                    ";
                            },'');
                        }
                    ],
                ]
            ]) ?>
            </div>
            <div class="col-lg-6 col-md-6">
                <?= GridView::widget([
                    'dataProvider' => $parMatchDP,
                    'columns' => [
                        'sportType',
                        [
                            'attribute' => 'matches',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return array_reduce($model['matches'], function ($carr, $item){
                                    $coefs = array_reduce( ArrayHelper::getValue($item,'coefs',[]) , function ($r, $i){
                                        return $r . "<div class='col-lg-2'><b>{$i['coefType']}</b> {$i['coefValue']}</div> ";
                                    },'');
                                    return $carr . "
                                            <div class='panel panel-default'>
                                                <div class='panel-body'>
                                                    <div class='row'>
                                                        <label>
                                                        <input type='radio' name='parimatch' value='{$item['matchId']}'>
                                                        <a data-match-id='{$item['matchId']}' >{$item['name']}</a>
                                                        </label>
                                                        
                                                    </div>
                                                    <div class='row'>
                                                        {$coefs}
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        ";
                                },'');
                            }
                        ],
                    ]
                ]) ?>
            </div>
        </div>
        <div class="row"><?= \yii\helpers\Html::submitButton('Submit',['class' => 'btn btn-primary']) ?></div>
        <?php \yii\widgets\ActiveForm::end() ?>

    </div>
</div>
