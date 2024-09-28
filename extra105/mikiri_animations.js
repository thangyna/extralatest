var posX1 = 0;  // 背景の位置
var posX2 = 0;
var posX3 = 0;
var posX4 = 0;
var skyX = 0;
var groundX = 0;

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
    groundX -= 1.5;

    elem1.style.backgroundPosition = posX1 + "px 0px";  // 背景の位置を更新
    elem2.style.backgroundPosition = posX2 + "px 0px";
    elem3.style.backgroundPosition = posX3 + "px 0px";
    elem4.style.backgroundPosition = posX4 + "px 0px";
    sky.style.backgroundPosition = skyX + "px 0px";
    ground.style.backgroundPosition = groundX + "px 0px";
    ground.style.backgroundPosition = bottom;

    requestAnimationFrame(scrollBackground);  // アニメーションを継続
}

// ページ読み込み後に背景スクロールを開始
window.onload = function() {
  requestAnimationFrame(scrollBackground);
};