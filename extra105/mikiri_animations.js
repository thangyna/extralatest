// 背景座標
var pos_elem1 = 0;
var pos_elem2 = 0;
var pos_elem3 = 0;
var pos_elem4 = 0;
var pos_elemSky = 0;

// キャラクターアニメーション用
var i = 0;
var isPlaying = true;
var characterState = 'run';  // 初期ステートを 'run' に設定
const chara = document.getElementById("character");

// キャラクターフレーム
const characterImages = {
  run: [
    "chara/knight/run/Run_0.png",
    "chara/knight/run/Run_1.png",
    "chara/knight/run/Run_2.png",
    "chara/knight/run/Run_3.png",
    "chara/knight/run/Run_4.png",
    "chara/knight/run/Run_5.png",
    "chara/knight/run/Run_6.png",
    "chara/knight/run/Run_7.png"
  ],
  attack: [
    "chara/knight/attack/Attack_0.png",
    "chara/knight/attack/Attack_1.png",
    "chara/knight/attack/Attack_2.png",
    "chara/knight/attack/Attack_3.png",
    "chara/knight/attack/Attack_4.png",
    "chara/knight/attack/Attack_5.png",
    "chara/knight/attack/Attack_6.png"
  ],
  death: [
    "chara/knight/death/Death_01.png",
    "chara/knight/death/Death_02.png",
    "chara/knight/death/Death_03.png",
    "chara/knight/death/Death_04.png",
    "chara/knight/death/Death_05.png",
    "chara/knight/death/Death_06.png",
    "chara/knight/death/Death_07.png",
    "chara/knight/death/Death_08.png",
    "chara/knight/death/Death_09.png",
    "chara/knight/death/Death_10.png",
    "chara/knight/death/Death_11.png"
  ]
};


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
function animateCharactor() {
  // フレーム間のマイクロ秒
  var imageFrame = 100;

  // 画像を更新
  function updateImage() {
    // 画像
    var img = characterImages[characterState][i];
    chara.src = img;

    // 現在のフレーム、ステートで分岐
    if (isPlaying) {
      i = (i + 1) % characterImages[characterState].length;
    }
    switch (characterState) {
      case 'run': animation_run(); break;
      case 'attack': animation_attack(); break;
      case 'death': animation_death(); break;
    }
  }
  setInterval(updateImage, imageFrame);  // Change image every 100ms (10 frames per second)
}


function animation_run() {

}
function animation_attack() {
  if (i === 4)
    moveCharacter("80%", "0.1");
  // 攻撃アニメーションが終わったら、ステートを 'run' に戻す
  if (i === 0) {
    setAnimationState('run');
    moveCharacter("30%", "5")
  }
}
function animation_death() {
  if (i === characterImages[characterState].length - 1) {
    isPlaying = false;
  }
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
function setAnimationState(state) {
  characterState = state;
  i = 0;
}

//----------------------------------------------------------------------------------------------------
//  サイトのロードが終了したとき、アニメーションを開始
//----------------------------------------------------------------------------------------------------
window.onload = function () {
  requestAnimationFrame(scrollBackground);
  requestAnimationFrame(animateCharactor);
};