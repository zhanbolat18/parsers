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
                        <div class="col-lg-3 col-md-3 ">
                            <label>
                                Победа первого
                                <input class="first" type="text" readonly value="<?= $olimpCoef['p1'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label>
                                Ничья
                                <input class="neutral" type="text" readonly value="<?= $olimpCoef['x'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label>
                                Победа второго
                                <input class="second" type="text" readonly value="<?= $olimpCoef['p2'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            TOTAL <span class="total-coef"><?= $olimpCoef['total'] ?></span>
                            <label>
                                Under
                                <input class="total total-under" type="text" readonly value="<?= $olimpCoef['under'] ?>">
                            </label>
                            <label>
                                Over
                                <input class="total total-over" type="text" readonly value="<?= $olimpCoef['over'] ?>">
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
                        <div class="col-lg-3 col-md-3">
                            <label>
                                Победа первого
                                <input class="first" type="text" readonly value="<?= $parimatchCoef['p1'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label>
                                Ничья
                                <input class="neutral" type="text" readonly value="<?= $parimatchCoef['x'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label>
                                Победа второго
                                <input class="second" type="text" readonly value="<?= $parimatchCoef['p2'] ?>">
                            </label>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            TOTAL <span class="total-coef"><?= $parimatchCoef['total'] ?></span>
                            <label>
                                Under
                                <input class="total total-under" type="text" readonly value="<?= $parimatchCoef['under'] ?>">
                            </label>
                            <label>
                                Over
                                <input class="total total-over" type="text" readonly value="<?= $parimatchCoef['over'] ?>">
                            </label>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row results">

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
                    
                    $(\'.olimp-panel .total.total-under\').val(olimp.under);
                    $(\'.parimatch-panel .total.total-under\').val(parimatch.under);
                    
                    $(\'.olimp-panel .total.total-over\').val(olimp.over);
                    $(\'.parimatch-panel .total.total-over\').val(parimatch.over);
                    
                    var flsr = calculate(olimp.p1, parimatch.p2);
                    var frsl = calculate(parimatch.p1, olimp.p2);
                    var flsrTotal = calculate(olimp.under, parimatch.over);
                    var frslTotal = calculate(parimatch.under, olimp.over);
                    
                    $(\'div.results\').html("<b>Olimp[П1]</b>: " + flsr.p1_bet + " - " + flsr.p2_bet + " <b>Parmiatch[П2]</b>" + " Percet: " + flsr.percent +
                            "<br><b>Parmiatch[П1]</b>: " + frsl.p1_bet + " - " + frsl.p2_bet + " <b>Olimp[П1]</b>" + " Percet: " + frsl.percent + 
                            "<br><b>Olimp[Under]</b>: " + flsrTotal.p1_bet + " - " + flsrTotal.p2_bet + " <b>Parmiatch[Over]</b>" + " Percet: " + flsrTotal.percent + 
                            "<br><b>Parmiatch[Under]</b>: " + frslTotal.p1_bet + " - " + frslTotal.p2_bet + "<b> Olimp[Over]</b>" + " Percet: " + frslTotal.percent 
                            );
                    $(\'.olimp-panel span.total-coef\').val(olimp.total);
                    $(\'.parimatch-panel span.total-coef\').val(parimatch.total);
                    console.log(\'Update Fork:\');
                    console.log(\'Olimp P1, Pari P2:\', \'Fork:\', flsr.fork , \'P1 Bet:\', flsr.p1_bet, \'P2 Bet:\', flsr.p2_bet, \'Percent:\', flsr.percent);
                    console.log(\'Pari P1, Olimp P2:\', \'Fork:\', frsl.fork , \'P1 Bet:\', frsl.p1_bet, \'P2 Bet:\', frsl.p2_bet, \'Percent:\', flsr.percent);
                })
                .fail(function (e) {
                    console.error(e.responseText)
                })
        },2000);
');