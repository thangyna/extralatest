// ビジュアライズ
let levelBarPar = 0;
let levelBar = document.getElementById("level-bar");
// 計算式
let mult= 5;
const level = (_value) => {return Math.floor(Math.sqrt(_value/mult));};
const min = (_level) => Math.pow(_level, 2) * mult;
const max = (_level) => Math.pow(_level + 1, 2) * mult;
// level
let currentExp = 0;

/*------------------------------------------------
    levelのビジュアライズ
------------------------------------------------*/
function addExp(_additionalExp) {
    let currentLevel = level(currentExp);
    let newLevel = level(currentExp + _additionalExp);
    let isLevelUp = currentLevel < newLevel;
    console.log("currentExp: " + currentExp);
    currentExp += _additionalExp;
    console.log("newExp: " + currentExp);
    console.log("currentLevel: " + currentLevel);
    console.log("newLevel: " + newLevel);

    if (isLevelUp) {
        console.log("Level Up!");
        alterLevel(100);
        setTimeout(() => {
            levelBar.style.transition = "width 0ms";
            alterLevel(0);
            setTimeout(() => {
                levelBar.style.transition = "width 300ms";
                alterLevel(calculateLevelPar(currentExp));
            }, 1);
        }, 300);
    }
    else {
        alterLevel(calculateLevelPar(currentExp));
    }
    console.log("levelBarPar: " + calculateLevelPar(currentExp));
    console.log("------------------------------------------------");
}

function calculateLevelPar(_value) {
    // levelの値の割合を算出する
    let newLevel = level(_value);
    let _min = min(newLevel);
    let _max = max(newLevel);
    let reqiredExp = _max - _min;
    let _currentExp = _value - _min;
    console.log("min: " + _min);
    console.log("max: " + _max);
    console.log("reqiredExp: " + reqiredExp);
    console.log("_currentExp: " + _currentExp);
    return (_currentExp / reqiredExp) * 100;
}

function alterLevel(_levelBarPar) {
    // 算出の結果 0 以下になった場合
    if (_levelBarPar <= 0) {
        _levelBarPar = 0;
    } else if (_levelBarPar > 100) {
        // 算出の結果 100 を超過した場合
        _levelBarPar = 100;
    }

    levelBar.style.width = _levelBarPar + "%";
}

/*------------------------------------------------
    levelの計算
------------------------------------------------*/
document.addEventListener('DOMContentLoaded', function() {
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('user_settings.php')
        .then(response => response.json())
        .then(data => {
            console.log("username:" + data.username);
            console.log("exp:" + data.exp);
            /*------------------------------------------------
                最少スコアの初期状態
            ------------------------------------------------*/
            if (!useMinScoreCheckbox.checked) {
                minScoreInput.disabled = true;
                minScoreInput.style.backgroundColor = '#e0e0e0';
            }
        });
});