/*==================================================以下変数==================================================*/
// 背景座標
var pos_elem1 = 0;
var pos_elem2 = 0;
var pos_elem3 = 0;
var pos_elem4 = 0;
var pos_elemSky = 0;

// characterImages
const documentImg = {
  knight: document.getElementById("character"),
  bandit: document.getElementById("enemy")
}

const framecount = {
    knight: {
        run: 8,
        death: 10,
        attack: 7,
        idle: 11
    },
    bandit: {
        run: 8,
        idle: 4,
        fightidle: 4
    }
}
const currentFrame = {
    knight: 0,
    bandit: 0
}
const frameToChange = {
    knight: 100,
    bandit: 50
}
const chara = {
    knight: {
        run: [],
        death: [],
        attack: [],
        idle: []
    },
    bandit: {
        run: [],
        idle: [],
        fightidle: []
    }
}

// character animation
var isPlaying = true;
// 画像をオブジェクトに格納する関数
function loadImages(_chara ,_action) {
    const count = framecount[_chara][_action]; // 指定されたアクションのフレーム数を取得
    for (let i = 0; i < count; i++) {
        // 画像のパスを生成（例: 'path/to/run1.png'）
        const imagePath = `chara/${_chara}/${_action}/${_action}_${i}.png`;
        chara[_chara][_action].push(imagePath); // knightオブジェクトの該当アクションに画像パスを追加
    }
}

/*==================================================以下関数==================================================*/

//---------------------------------------------------------------------------------------------------- 
//  背景をスクロールする関数
//----------------------------------------------------------------------------------------------------
function scrollBackground() {
    var elem1 = document.getElementById("background_1");
    var elem2 = document.getElementById("background_2");
    var elem3 = document.getElementById("background_3");
    var elem4 = document.getElementById("background_4");
    var elemSky = document.getElementById("sky");

    pos_elem1 -= 1.5;  // 背景を左に1ピクセルずつ移動
    pos_elem2 -= 1;
    pos_elem3 -= 0.5;
    pos_elem4 -= 0.3;
    pos_elemSky -= 0.1;

    elem1.style.backgroundPosition = pos_elem1 + "px 0px";  // 背景の位置を更新
    elem2.style.backgroundPosition = pos_elem2 + "px 0px";
    elem3.style.backgroundPosition = pos_elem3 + "px 0px";
    elem4.style.backgroundPosition = pos_elem4 + "px 0px";
    elemSky.style.backgroundPosition = pos_elemSky + "px 0px";

    requestAnimationFrame(scrollBackground);  // アニメーションを継続
}

//----------------------------------------------------------------------------------------------------
//  キャラクターアニメーション
//----------------------------------------------------------------------------------------------------
function animateCharactor(_chara, _state) {
    var frameSpeed = frameToChange[_chara]; // time to take change frame (micro second)

    // 画像を更新
    function updateImage() {
        // 画像
        var img = chara[_chara][_state][currentFrame[_chara]];
        documentImg[_chara].src = img;

        // 現在のフレーム、ステートで分岐
        if (isPlaying) {
            currentFrame[_chara] = (currentFrame[_chara] + 1) % chara[_chara][_state].length;
        }
        console.log("chara: " + _chara + "   state: " + _state);
    }
    setInterval(() => updateImage(_chara, _state), frameSpeed);  // Change image every 100ms (10 frames per second)
}

// キャラクターを移動させる関数
function moveCharacter(where, speed) {
    var character = document.getElementById("character");
    character.style.transition = "left " + speed + "s";  // 1秒かけて移動
    character.style.left = where;
}

//----------------------------------------------------------------------------------------------------
//  ステートを設定
//----------------------------------------------------------------------------------------------------
function setAnimation_state(state) {
    character_state = state;
    currentFrame = 0;
}

//----------------------------------------------------------------------------------------------------
//  サイトのロードが終了したとき、アニメーションを開始
//----------------------------------------------------------------------------------------------------
window.onload = function () {
    requestAnimationFrame(scrollBackground);
    requestAnimationFrame(() => animateCharactor('knight', 'idle'));
    requestAnimationFrame(() => animateCharactor('bandit', 'fightidle'));

    // 各アクションに対して画像をロード
    loadImages('knight', 'run');
    loadImages('knight', 'death');
    loadImages('knight', 'attack');
    loadImages('knight', 'idle');
    loadImages('bandit', 'run');
    loadImages('bandit', 'idle');
    loadImages('bandit', 'fightidle');
};
