function updateImageContainerHeight() {
    const keyboard = document.getElementById('keyboard');
    const imageContainer = document.querySelector('.keyboard-container');
    const keyboardHeight = keyboard.offsetHeight;
    imageContainer.style.height = `${keyboardHeight}px`;
}

// 初期化時に高さを設定
updateImageContainerHeight();

// ウィンドウのリサイズイベントにリスナーを追加
window.addEventListener('resize', updateImageContainerHeight);