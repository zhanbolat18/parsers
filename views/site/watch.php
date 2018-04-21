<?php
/* @var $olimpCoef array*/
/* @var $parimatchCoef array*/

use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$this->title = 'Parser';
$this->registerJsFile('/js/watcher.js',['depends' => \yii\web\JqueryAsset::class]);
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-header">
                        <?= $olimpCoef['name'] ?>
                    </div>
                    <div class="panel-body olimp-panel">
                        <div class="col-lg-4 col-md-4 ">
                            <label>
                                Победа первого
                                <input class="first" type="text" readonly value="<?= $olimpCoef['p1'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>
                                Ничья
                                <input class="neutral" type="text" readonly value="<?= $olimpCoef['x'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>
                                Победа второго
                                <input class="second" type="text" readonly value="<?= $olimpCoef['p2'] ?>">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-header">
                        <?= $parimatchCoef['name'] ?>
                    </div>
                    <div class="panel-body parimatch-panel">
                        <div class="col-lg-4 col-md-4">
                            <label>
                                Победа первого
                                <input class="first" type="text" readonly value="<?= $parimatchCoef['p1'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>
                                Ничья
                                <input class="neutral" type="text" readonly value="<?= $parimatchCoef['x'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>
                                Победа второго
                                <input class="second" type="text" readonly value="<?= $parimatchCoef['p2'] ?>">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
