var posX1 = 0;  // 背景の位置
var posX2 = 0;
var posX3 = 0;
var posX4 = 0;
var skyX = 0;


// current img frame
var i = 0;
const chara = document.getElementById("character");
var characterState = 'run';  // 初期ステートを 'run' に設定
const characterImages = {
  run: ["chara/knight/run/Run_0.png", "chara/knight/run/Run_1.png", "chara/knight/run/Run_2.png", "chara/knight/run/Run_3.png", "chara/knight/run/Run_4.png", "chara/knight/run/Run_5.png", "chara/knight/run/Run_6.png", "chara/knight/run/Run_7.png"],
  attack: ["chara/knight/attack/Attack_0.png", "chara/knight/attack/Attack_1.png", "chara/knight/attack/Attack_2.png", "chara/knight/attack/Attack_3.png", "chara/knight/attack/Attack_4.png", "chara/knight/attack/Attack_5.png", "chara/knight/attack/Attack_6.png"]
};

/*--------------------------------------------------
  背景をスクロールする関数
--------------------------------------------------*/
function scrollBackground() {
  var elem1 = document.getElementById("background_1");
  var elem2 = document.getElementById("background_2");
  var elem3 = document.getElementById("background_3");
  var elem4 = document.getElementById("background_4");
  var sky = document.getElementById("sky");
  var ground = document.getElementById("ground");

  posX1 -= 1.5;  // 背景を左に1ピクセルずつ移動
  posX2 -= 1;
  posX3 -= 0.5;
  posX4 -= 0.3;
  skyX -=  0.1;

  elem1.style.backgroundPosition = posX1 + "px 0px";  // 背景の位置を更新
  elem2.style.backgroundPosition = posX2 + "px 0px";
  elem3.style.backgroundPosition = posX3 + "px 0px";
  elem4.style.backgroundPosition = posX4 + "px 0px";
  sky.style.backgroundPosition = skyX + "px 0px";

  requestAnimationFrame(scrollBackground);  // アニメーションを継続
}

function animateCharactor() {
  function updateImage() {
    var img = characterImages[characterState][i];
    chara.src = img;
    i = (i + 1) % characterImages[characterState].length;
    switch (characterState) {
      case 'run':
        break;
      case 'attack':
        // 攻撃アニメーションの途中で、キャラクターを右に移動させる
        if (i === 4)
          moveCharacter("80%", "0.1");
        // ヒットストップ
        if (i === characterImages[characterState].length - 1) {
          //await sleep(1000);  // 1秒待つ
        }
        // 攻撃アニメーションが終わったら、ステートを 'run' に戻す
        if (i === 0) {
          setAnimationState('run');
          moveCharacter("30%", "5")
        }
        break;
      }
  }

  setInterval(updateImage, 100);  // Change image every 100ms (10 frames per second)
}

// キャラクターのアニメーションステートを設定する関数
function setAnimationState(state) {
  characterState = state;
  i = 0;
}

// キャラクターを移動させる関数
function moveCharacter(where, speed) {
  var character = document.getElementById("character");
  character.style.transition = "left " + speed +"s";  // 1秒かけて移動
  character.style.left = where;
}

// ページ読み込み後に背景スクロールを開始
window.onload = function() {
  requestAnimationFrame(scrollBackground);
  requestAnimationFrame(animateCharactor);
};