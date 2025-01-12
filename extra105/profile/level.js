// ビジュアライズ
let levelBarPar = 0;
let levelBar = document.getElementById("level-bar");
// 計算式
let mult= 5;
const level = (value) => {return math.floor(mult * value * value);};
// level
let value = 100;

/*------------------------------------------------
    levelのビジュアライズ
------------------------------------------------*/
function calculateLevel(_max, _value ,_min) {
    let reqiredExp = _max - _min;
    let currentExp = _value - _min;
    return currentExp / reqiredExp * 100;
}

function alterLevel(_value) {
    // levelの値を算出する
    levelBarPar += _value
    if (levelBarPar <= 0) {
        // 算出の結果 0 以下になった場合
        levelBarPar = 0
    } else {
        // 算出の結果 100 を超過した場合
        if (levelBarPar > 100) {
            levelBarPar = 100
        }
    }
    // スタイル(幅)を更新する
    levelBar.style.width = levelBarPar + "%"
}

/*------------------------------------------------
    levelの計算
------------------------------------------------*/

document.addEventListener('DOMContentLoaded', function() {
    console.log("level.js: DOMContentLoaded")
    alterLevel(calculateLevel(200, 120, 100));

    console.log("current level: " + level(value));
});