var posX = 0;

function scrollBackground() {
  var elem = document.getElementById("background");
  posX -= 1;  // 背景を左に1ピクセルずつ移動

  elem.style.backgroundPosition = posX + "px 0px";  // 背景の位置を更新

  requestAnimationFrame(scrollBackground);  // アニメーションを継続
}

// ページ読み込み後に背景スクロールを開始
window.onload = function() {
  requestAnimationFrame(scrollBackground);
};