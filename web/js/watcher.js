(function (_) {
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


        var flsr = 1 / ((1 / olimpCoefs.first) + (1 / parimatchCoefs.second));
        var frsl = 1 / ((1 / parimatchCoefs.first) + (1 / olimpCoefs.second));



        var p1stavka = (1 / olimpCoefs.first * flsr * 100).toFixed();
        var p2stavka = (1 / parimatchCoefs.second * flsr * 100).toFixed();
        var procentPlus = ((flsr - 1) * 100).toFixed(2);

        console.log('p1 stav:', p1stavka);
        console.log('p2 stav:', p2stavka);
        console.log('Percent:', procentPlus);


    }

    _(document).ready(function () {
        fork();
    });
}(jQuery));