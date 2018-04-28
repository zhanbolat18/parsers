function calculate(p1, p2) {
    var _fork = 1 / ((1 / p1) + (1 / p2));
    return {
        fork : _fork,
        p1_bet : (1 / p1 * _fork * 100).toFixed(),
        p2_bet : (1 / p2 * _fork * 100).toFixed(),
        percent :((_fork - 1) * 100).toFixed(2)
    };
}
(function (_) {
    _.extend({
        calculate : calculate
    });
    function fork() {

        var olimpPanel = _('.olimp-panel'), parimatchPanel = _('.parimatch-panel'),
            olimpCoefs = {
                first : parseFloat(olimpPanel.find('input.first').val()),
                second : parseFloat(olimpPanel.find('input.second').val()),
                x : parseFloat(olimpPanel.find('input.neutral').val())
            },
            parimatchCoefs = {
                first : parseFloat(parimatchPanel.find('input.first').val()),
                second : parseFloat(parimatchPanel.find('input.second').val()),
                x : parseFloat(parimatchPanel.find('input.neutral').val())
            };

        var flsr = calculate(olimpCoefs.first, parimatchCoefs.second);
        var frsl = calculate(parimatchCoefs.first, olimpCoefs.second);


        console.log('Update Fork:');
        console.log('Olimp P1, Pari P2:');
        console.log('Fork:', flsr.fork , 'P1 Bet:', flsr.p1_bet, 'P2 Bet:', flsr.p2_bet, 'Percent:', flsr.percent);

        console.log('Pari P1, Olimp P2:');
        console.log('Fork:', frsl.fork , 'P1 Bet:', frsl.p1_bet, 'P2 Bet:', frsl.p2_bet, 'Percent:', flsr.percent);


    }

    _(document).ready(function () {
        fork();
    });
}(jQuery));