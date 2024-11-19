/*============================== 以下ボタンが押されたときのアニメーション ==============================*/
document.addEventListener('DOMContentLoaded', function() {
    const playButton = document.getElementById('playButton');
    const quitButton = document.getElementById('quitButton');
    const cancelButton = document.getElementById('cancelButton');
    const playButtonImage = document.getElementById('playButtonImage');
    const quitButtonImage = document.getElementById('quitButtonImage');
    const cancelButtonImage = document.getElementById('cancelButtonImage');

    function changeImage(button) {
        if (button === 'play') {
            playButtonImage.src = 'button/play_down.png';
        } else if (button === 'quit') {
            quitButtonImage.src = 'button/quit_down.png';
        } else if (button === 'cancel') {
            cancelButtonImage.src = 'button/mini/green_x_down.png';
        }
    }

    function resetImage(button) {
        if (button === 'play') {
            playButtonImage.src = 'button/play.png';
        } else if (button === 'quit') {
            quitButtonImage.src = 'button/quit.png';
        } else if (button === 'cancel') {
            cancelButtonImage.src = 'button/green_x_up.png';
        }
    }
    
    // {}内がボタンが押されたときの処理
    // マウスのボタンが押されたときの処理
    playButton.addEventListener('mousedown', () => {
        changeImage('play');
    });
    // マウスのボタンが離されたときの処理
    playButton.addEventListener('mouseup', () => resetImage('play'));
    // マウスがボタンから離れたときの処理
    playButton.addEventListener('mouseleave', () => resetImage('play'));

    quitButton.addEventListener('mousedown', () => changeImage('quit'));
    quitButton.addEventListener('mouseup', () => resetImage('quit'));
    quitButton.addEventListener('mouseleave', () => resetImage('quit'));
});
