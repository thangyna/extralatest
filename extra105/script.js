// ゲームシステム関連
let isPlaying = false;
let doRecord = true;
let countdown = 3;
let countdownTimer;
let timeLimitStart = 60;
let timeLimit;  // 制限時間60秒
let timer;

// ゲームプレイ関連
let correctChars = 0;
let mistakes = 0;
let score = 0;
let typeSpeed = 0;
let correctivity = 0;
let nextChar = "";
let mistakesCount = {};

let words = [];
let wordIndex = 0;  // 現在の問題のインデックス
let shuffledWords = [];  // シャッフルされた問題のリスト

// ウェブサイトのビジュアライズ関連
const startButton = document.getElementById('startButton');
const countdownText = document.getElementById('countdown');
const japaneseWord = document.getElementById('japaneseWord');
const kanaWord = document.getElementById('kanaWord');
const romajiWord = document.getElementById('romajiWord');
const scoreNumText = document.getElementById('scoreNum');  // Add this line
const mistakesNumText = document.getElementById('mistakesNum');  // Add this line

// キーボードの表示
const keyboard = document.getElementById('keyboard');
const key = {
    "a": document.getElementById("A"),
    "b": document.getElementById("B"),
    "c": document.getElementById("C"),
    "d": document.getElementById("D"),
    "e": document.getElementById("E"),
    "f": document.getElementById("F"),
    "g": document.getElementById("G"),
    "h": document.getElementById("H"),
    "i": document.getElementById("I"),
    "j": document.getElementById("J"),
    "k": document.getElementById("K"),
    "l": document.getElementById("L"),
    "m": document.getElementById("M"),
    "n": document.getElementById("N"),
    "o": document.getElementById("O"),
    "p": document.getElementById("P"),
    "q": document.getElementById("Q"),
    "r": document.getElementById("R"),
    "s": document.getElementById("S"),
    "t": document.getElementById("T"),
    "u": document.getElementById("U"),
    "v": document.getElementById("V"),
    "w": document.getElementById("W"),
    "x": document.getElementById("X"),
    "y": document.getElementById("Y"),
    "z": document.getElementById("Z"),
    "-": document.getElementById("hyphen")
};
// タイムリミットバー
const timerText = document.getElementById('timer')          // タイマー
const timeBar = document.getElementById('time-bar')         // タイムリミットバー
timeBar.style.width = "100%"                                // タイムリミット 初期幅
let timeBarPar = 100                                        // 残り時間 初期値

/*------------------------------------------------
    ゲーム設定
------------------------------------------------*/
// スタートのフラグ
function startGame() {
    console.log("startGame");
    if (isPlaying) 
        return;

    // ウェブサイトの画面を初期化
    startButton.innerText = "リスタート";

    countdownText.innerText = countdown;
    japaneseWord.innerText = "問題文を待っています...";
    romajiWord.innerText = "もんだいぶんをまっています...";

    isPlaying = true;
    doRecord = true;
    correctChars = 0;
    mistakes = 0;
    score = 0;
    typeSpeed = 0;
    correctivity = 0;

    countdown = 3;
    countdownText.innerText = countdown;

    // 問題リストをシャッフルしておく
    shuffledWords = shuffleArray(words.slice());
    wordIndex = 0;

    // 3秒のカウントダウン
    countdownTimer = setInterval(() => {
        countdown--;
        countdownText.innerText = countdown;

        if (countdown <= 0) {
            clearInterval(countdownTimer);
            countdownText.innerText = "";
            alterTime(-100 / timeLimitStart); // タイムリミットのゲージ
            startTypingGame();
        }
    }, 1000);
}

// メインゲームを開始
function startTypingGame() {
    console.log("startTypingGame");
    timeLimit = timeLimitStart;
    timerText.innerText = timeLimit;
    // プレイヤーデータの初期化
    currentPosition = 0;
    currentRomajiIndex = 0;
    setNextWord();  // ゲーム開始時に問題を表示

    /*------------------------------------------------
        タイムリミット処理
    ------------------------------------------------*/
    timer = setInterval(() => {
        timeLimit--;
        timerText.innerText = timeLimit;
        alterTime(-100 / timeLimitStart); // タイムリミットのゲージ
        // タイムリミットが 0 になった場合
        if (timeLimit <= 0) {
            endGame(true);
        }
    }, 1000);
}

// ゲームを終了
function endGame(_doRecord) {
    console.log("endGame");
    clearInterval(timer);
    timeLimit = 0;
    if(_doRecord) {
        let accuracy = calculateAccuracy(correctChars, mistakes);
        let typingSpeed = calculateTypingSpeed(correctChars, timeLimitStart);
        let topMistakes = getTopMistakes(mistakesCount);
        saveGameResults(score, correctChars, mistakes);
        alert("ゲーム終了！\nスコア: " + score + "\n正しく打てた文字数: " + correctChars + "\n間違った文字数: " + mistakes +
                "\n正解率: " + accuracy + "%\n打鍵数: " + typingSpeed + "/"+ timeLimitStart + "秒" +"\n間違えやすいキー: " + topMistakes.replace(/,/g, ', '));
    }
    isPlaying = false;
    setNextGame();
}
// 次のゲームをセット
function setNextGame() {
    console.log("setNextGame");
    // 問題リストを読み込む
    loadWords();
    // ウェブサイトの画面を設定
    startButton.innerText = "スタート";
    countdownText.innerText = "入力されたデータは収集される場合があります";
    japaneseWord.innerText = "前回のスコア: " + score;
    kanaWord.innerText = "スタート/エンターキーで開始";

    // ゲームの初期化
    isPlaying = false;
    doRecord = true;
    clearInterval(countdownTimer);
    timerText.innerText = timeLimitStart;
    alterTime(100); // タイムリミットのゲージを満タンにする
    score = 0;
    typeSpeed = 0;
    correctivity = 0;
    mistakes = 0;
    nextChar = "";
    for (_key in key) {
        if (key[_key]) {  // Add this null check
            key[_key].style.display = "none";
        }
    }
}

// JSONファイルからwordsを読み込む関数
function loadWords() {
    return fetch('words.json')
        .then(response => response.json())
        .then(data => {
            words = data;
            shuffledWords = shuffleArray(words.slice());
        })
        .catch(error => console.error('Error loading words:', error));
}

// 配列をシャッフルする関数
function shuffleArray(_array) {
    for (let i = _array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [_array[i], _array[j]] = [_array[j], _array[i]];
    }
    return _array;
}

/*------------------------------------------------
    メインゲーム
------------------------------------------------*/
function processInput(_inputChar) {
    let isCorrect = false;
    // 入力文字が正しいかどうかを判定
    for (let i = 0; i < currentRomaji.length; i++) {
        let romaji = currentRomaji[i];
        // 入力されたキーがローマ字の最初の文字と一致する場合
        if (keyHistory + _inputChar === romaji.substring(0, currentPosition + 1)) {
            isCorrect = true;
            nextChar = romaji.substring(currentPosition + 1, currentPosition + 2);
            currentRomajiIndex = i;
            currentPosition++;
            keyHistory = keyHistory + _inputChar;
            updateRomajiWord(romaji);
            break;
        }
    }

    // 入力文字が正しくない場合
    if (!isCorrect) {
        mistakes++;
        mistakesCount[_inputChar] = (mistakesCount[_inputChar] || 0) + 1;
        // ミス時にローマ字ワードをハイライト
        romajiWord.style.backgroundColor = "orange";
        setTimeout(() => {
            romajiWord.style.backgroundColor = "transparent";
        }, 100);

        if (mistakesNumText) {  // Add this check
            mistakesNumText.innerText = mistakes;
        }
    } else { // 入力文字が正しい場合
        correctChars++;
        // 正解時にハイライトをキャンセル
        romajiWord.style.backgroundColor = "transparent";
        if (scoreNumText) {  // Add this check
            scoreNumText.innerText = score;
        }

        // 全ての文字が正しく入力された場合
        if (currentPosition === currentRomaji[currentRomajiIndex].length) {
            console.log("nextWord");
            setNextWord();
            return;  // Add this line to exit the function after setting next word
        }
        for (_key in key) {
            if (key[_key]) {  // Add this null check
                key[_key].style.display = "none";
            }
        }
        if (key[nextChar]) {  // Add this null check
            key[nextChar].style.display = "block";
        }
    }
    
    // スコアの計算
    score = calculateScore(correctChars, mistakes, timeLimit);
}

// スコアの計算
function calculateScore(_correctChars, _mistakes, _currentTime) {
    let allInputChars = _correctChars + _mistakes;
    // 正確性
    correctivity = _correctChars / allInputChars;
    // タイプスピード
    typeSpeed = (60 / _currentTime) * _correctChars;
    let _score = typeSpeed * (correctivity * 100);
    console.log(typeSpeed + "*" + "(" + correctivity + "*" + 100 + ")" )
    return Math.floor(_score);
}

// 次の問題をセット
function setNextWord() {
    if (wordIndex >= shuffledWords.length) {
        // すべての問題が出題されたら再度シャッフル
        shuffledWords = shuffleArray(words.slice());
        wordIndex = 0;
    }

    let wordData = shuffledWords[wordIndex];
    currentWord = wordData.japanese;
    currentRomaji = wordData.romaji;
    currentKana = wordData.kana;
    currentRomajiIndex = 0;
    japaneseWord.innerText = currentWord;  // 問題文を表示
    kanaWord.innerHTML = currentKana;
    currentPosition = 0;
    keyHistory = "";
    nextChar = currentRomaji[currentRomajiIndex][0];  // Change this line
    
    // キーボード表示をリセット
    for (let _key in key) {
        if (key[_key]) {
            key[_key].style.display = "none";
        }
    }
    if (key[nextChar]) {
        key[nextChar].style.display = "block";
    }
    
    updateRomajiWord(currentRomaji[currentRomajiIndex]);

    wordIndex++;
    
    //console.log("New word set:", currentWord, currentRomaji);  // Add this line for debugging
}

/*------------------------------------------------
    ゲームの結果を保存する
------------------------------------------------*/
function calculateAccuracy(correctChars, mistakes) {
    return (correctChars / (correctChars + mistakes) * 100).toFixed(2);
}

function calculateTypingSpeed(correctChars, timeLimit) {
    return Math.round(correctChars / (60 - timeLimit) * 60);
}

function getTopMistakes(mistakesObj) {
    return Object.entries(mistakesObj)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5)
        .map(([key, value]) => `${key}:${value}`)
        .join(',');
}

function saveGameResults(_score, _correctChars, _mistakes) {
    let accuracy = calculateAccuracy(_correctChars, _mistakes);
    let typingSpeed = calculateTypingSpeed(_correctChars, timeLimit);
    let topMistakes = getTopMistakes(mistakesCount);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "save_results.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // リクエストが完了した際の処理
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200)
{
            console.log(xhr.responseText);
        }
    };
    let params = "score=" + _score + "&correct_chars=" + _correctChars + "&mistakes=" + _mistakes +
                 "&accuracy=" + accuracy + "&typing_speed=" + typingSpeed + "&top_mistakes=" + encodeURIComponent(topMistakes);
    xhr.send(params);
}

/*------------------------------------------------
    ウェブサイトのビジュアライズ
------------------------------------------------*/
function alterTime(_value) {
    // timeの値を算出する
    timeBarPar += _value
    if (timeBarPar <= 0) {
        // 算出の結果 0 以下になった場合
        timeBarPar = 0
    } else {
        // 算出の結果 100 を超過した場合
        if (timeBarPar > 100) {
            timeBarPar = 100
        }
    }
    // スタイル(幅)を更新する
    timeBar.style.width = timeBarPar + "%"
}

// ローマ字の表示を更新
function updateRomajiWord(_romaji) {
    let highlightedText = keyHistory;
    let remainingText = _romaji.substring(currentPosition);
    // ハイライトされたテキストと残りのテキストを表示
    romajiWord.innerHTML = `<span id="highlightedText" style="color: blue;">${highlightedText}</span>${remainingText}`;
}

/*------------------------------------------------
    キー、ボタン入力の処理
------------------------------------------------*/
document.addEventListener("keypress", function (event) {
    let key = event.key;
    // エンターキーが押された場合
    if (key === "Enter") {
        // プレイ中の場合
        if (isPlaying) {
            endGame(false);
        }
        else {
            endGame(false);
            startGame();
        }
    }
    // プレイ中の場合
    else if (isPlaying) {
        processInput(event.key);
    }
});

startButton.addEventListener("click", function () {
    if (isPlaying) {
        endGame(false);
    }
    else {
        endGame(false);
        startGame();
    }
});

// Add this event listener at the end of your script
document.addEventListener('DOMContentLoaded', function() {
    setNextGame();  // Initialize the game after the DOM is fully loaded
});
