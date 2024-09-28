var posX = 0;

function scrollBackground() {
    var elem1 = document.getElementById("background_1");
    var elem2 = document.getElementById("background_2");
    var elem3 = document.getElementById("background_3");
    var elem4 = document.getElementById("background_4");
    var sky = document.getElementById("sky");

    posX -= 1;  // 背景を左に1ピクセルずつ移動

    elem1.style.backgroundPosition = posX + "px 0px";  // 背景の位置を更新
    elem2.style.backgroundPosition = posX + "px 0px";
    elem3.style.backgroundPosition = posX + "px 0px";
    elem4.style.backgroundPosition = posX + "px 0px";
    sky.style.backgroundPosition = posX + "px 0px";

    requestAnimationFrame(scrollBackground);  // アニメーションを継続
}

// ページ読み込み後に背景スクロールを開始
window.onload = function() {
  requestAnimationFrame(scrollBackground);
};