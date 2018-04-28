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
<?php
$this->registerJs('
        var olimpMatchId = ' .  $olimpMatchId . '
        var parimatchMatchId = ' . $parmiatchMatchId . '
        jQuery.extend({
            olimpMatchId : olimpMatchId,
            parimatchMatchId : parimatchMatchId
        });
        var interval = setInterval(function(){
            $.get(\'/site/update-coefs\',{olimp: olimpMatchId, parimatch : parimatchMatchId})
                .done(function (r) {
                    var olimp = r.olimp, parimatch = r.parimatch;
                    $(\'.olimp-panel .first\').val(olimp.p1);
                    $(\'.parimatch-panel .first\').val(parimatch.p1);
                    $(\'.olimp-panel .neutral\').val(olimp.x);
                    $(\'.parimatch-panel .neutral\').val(parimatch.x);
                    $(\'.olimp-panel .second\').val(olimp.p2);
                    $(\'.parimatch-panel .second\').val(parimatch.p2);
                    
                    var flsr = calculate(olimp.p1, parimatch.p2);
                    var frsl = calculate(parimatch.p1, olimp.p2);
            
            
                    console.log(\'Update Fork:\');
                    console.log(\'Olimp P1, Pari P2:\', \'Fork:\', flsr.fork , \'P1 Bet:\', flsr.p1_bet, \'P2 Bet:\', flsr.p2_bet, \'Percent:\', flsr.percent);
                    console.log(\'Pari P1, Olimp P2:\', \'Fork:\', frsl.fork , \'P1 Bet:\', frsl.p1_bet, \'P2 Bet:\', frsl.p2_bet, \'Percent:\', flsr.percent);
                })
                .fail(function (e) {
                    console.error(e.responseText)
                })
        },1000);
');