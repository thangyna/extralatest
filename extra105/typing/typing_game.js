// ゲームシステム関連
let isPlaying = false;
let doRecord = true;
let countdown = 3;
let countdownTimer;
let timeLimitStart = 60;
let timeLimit;
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

// ユーザ設定
let useHeighlight = true;
let isDisplay = true;

// ウェブサイトのビジュアライズ関連
const startButton = document.getElementById('startButton');
const countdownText = document.getElementById('countdown');
const wordArea = document.getElementById('wordArea');
const missDisplay = document.getElementById('missDisplay');
const japaneseWord = document.getElementById('japaneseWord');
const nextWord = document.getElementById('nextWord');
const kanaWord = document.getElementById('kanaWord');
const romajiWord = document.getElementById('romajiWord');
const scoreNumText = document.getElementById('scoreNum');  // Add this line
const mistakesNumText = document.getElementById('mistakesNum');  // Add this line

// キーボードの表示
const keyboard = document.getElementById('keyboard');
const key = {
    "a": document.getElementById("a"),
    "b": document.getElementById("b"),
    "c": document.getElementById("c"),
    "d": document.getElementById("d"),
    "e": document.getElementById("e"),
    "f": document.getElementById("f"),
    "g": document.getElementById("g"),
    "h": document.getElementById("h"),
    "i": document.getElementById("i"),
    "j": document.getElementById("j"),
    "k": document.getElementById("k"),
    "l": document.getElementById("l"),
    "m": document.getElementById("m"),
    "n": document.getElementById("n"),
    "o": document.getElementById("o"),
    "p": document.getElementById("p"),
    "q": document.getElementById("q"),
    "r": document.getElementById("r"),
    "s": document.getElementById("s"),
    "t": document.getElementById("t"),
    "u": document.getElementById("u"),
    "v": document.getElementById("v"),
    "w": document.getElementById("w"),
    "x": document.getElementById("x"),
    "y": document.getElementById("y"),
    "z": document.getElementById("z"),
    "-": document.getElementById("hyphen")
};
// タイムリミットバー
const timerText = document.getElementById('timer')          // タイマー
const timeBar = document.getElementById('time-bar')         // タイムリミットバー
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

    // キーのミスハイライトをリセット
    for (_key in key) {
        if (key[_key]) {
            key[_key].style.backgroundColor = "";
            console.log("キーのハイライトをリセット");
        }
    }

    // 3秒のカウントダウン
    countdownTimer = setInterval(() => {
        countdown--;
        countdownText.innerText = countdown;

        if (countdown <= 0) {
            clearInterval(countdownTimer);
            countdownText.innerText = "";
            animateTimeBar(0);
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
    showWord();
    setNextWord();  // ゲーム開始時に問題を表示

    /*------------------------------------------------
        タイムリミット処理
    ------------------------------------------------*/
    timer = setInterval(() => {
        timeLimit--;
        timerText.innerText = timeLimit;
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
        saveGameResults(score, correctChars, mistakes, isDisplay);
        alert(
            "ゲーム終了！\nスコア: " + score +
            "\n正しく打てた文字数: " + correctChars + 
            "\n間違った文字数: " + mistakes +
            "\n正解率: " + accuracy + 
            "%\n打鍵数: " + typingSpeed + "/"+ timeLimitStart + "秒" +
            "\n間違えやすいキー: " + topMistakes.replace(/,/g, ', ') +
            "\n公開: " + isDisplay,
        );
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
    countdownText.innerText = "";
    japaneseWord.innerText = "前回のスコア: " + score;
    kanaWord.innerText = "スタート/エンターキーで開始";

    nextWord.style.display = "none";
    romajiWord.style.display = "none";
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
    mistakesCount = {};
    for (_key in key) {
        if (key[_key]) {
            key[_key].classList.remove("highlight");
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
        mistakesCount[nextChar] = (mistakesCount[nextChar] || 0) + 1;
        // ミス時に画面を点滅させる
        missDisplay.style.backgroundColor = "orange";
        setTimeout(() => {
            missDisplay.style.backgroundColor = "transparent";
        }, 100);

        if (mistakesNumText) {  // Add this check
            mistakesNumText.innerText = mistakes;
        }
        // ミスしたキーをハイライト
        highlightMistakeKey(nextChar);
    } else { // 入力文字が正しい場合
        correctChars++;
        // 正解時にハイライトをキャンセル
        missDisplay.style.backgroundColor = "transparent";
        if (scoreNumText) {  // Add this check
            scoreNumText.innerText = score;
        }

        // 全ての文字が正しく入力された場合
        if (currentPosition === currentRomaji[currentRomajiIndex].length) {
            // console.log("nextWord");
            setNextWord();
            return;  // Add this line to exit the function after setting next word
        }
        for (_key in key) {
            if (key[_key]) {
                key[_key].classList.remove("highlight");
                // console.log(key[_key].classList);
            }
        }
        if (key[nextChar]) {
            key[nextChar].classList.add("highlight");
            // console.log(key[nextChar].classList);
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
    // console.log(typeSpeed + "*" + "(" + correctivity + "*" + 100 + ")" )
    return Math.floor(_score);
}

// 次の問題をセット
function setNextWord() {
    let wordData = shuffledWords[wordIndex];
    currentWord = wordData.japanese;
    currentRomaji = wordData.romaji;
    currentKana = wordData.kana;
    currentRomajiIndex = 0;
    japaneseWord.innerText = currentWord;  // 問題文を表示
    kanaWord.innerHTML = currentKana;

    if (wordIndex+1 >= shuffledWords.length) {
        // すべての問題が出題されたら再度シャッフル
        console.log("問題をシャッフル");
        shuffledWords = shuffleArray(words.slice());
        wordIndex = 0;
        // もし問題がかぶれば次の問題を出す
        if (currentWord == shuffledWords[wordIndex+1].japanese) {
            wordIndex++;
        }
    }

    nextWord.innerText = shuffledWords[wordIndex+1].japanese;
    currentPosition = 0;
    keyHistory = "";
    nextChar = currentRomaji[currentRomajiIndex][0];  // Change this line
    
    // キーボード表示をリセット
    for (let _key in key) {
        if (key[_key]) {
            key[_key].classList.remove("highlight");
        }
    }
    if (key[nextChar]) {
        key[nextChar].classList.add("highlight");
    }
    
    updateRomajiWord(currentRomaji[currentRomajiIndex]);

    wordIndex++;
}

/*------------------------------------------------
    ゲームの結果を保存する
------------------------------------------------*/
function calculateAccuracy(correctChars, mistakes) {
    return (correctChars / (correctChars + mistakes) * 100).toFixed(2);
}

function calculateTypingSpeed(correctChars, timeLimit) {
    return Math.round(correctChars / timeLimit);
}

function getTopMistakes(mistakesObj) {
    return Object.entries(mistakesObj)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5)
        .map(([key, value]) => `${key}:${value}`)
        .join(',');
}

function saveGameResults(_score, _correctChars, _mistakes ,_isDisplay) {
    let accuracy = calculateAccuracy(_correctChars, _mistakes);
    let typingSpeed = calculateTypingSpeed(_correctChars, timeLimit);
    let topMistakes = getTopMistakes(mistakesCount);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../save_results.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // リクエストが完了した際の処理
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText);
        }
    };
    let params = "score=" + _score + "&correct_chars=" + _correctChars + "&mistakes=" + _mistakes +
                 "&accuracy=" + accuracy + "&typing_speed=" + typingSpeed + "&top_mistakes=" + encodeURIComponent(topMistakes) +
                 "&is_display=" + _isDisplay;
    xhr.send(params);
}

/*------------------------------------------------
    ウェブサイトのビジュアライズ
------------------------------------------------*/
function alterTime(_value) {
    timeBar.style.transition = "none"
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

function animateTimeBar(_var) {
    timeBar.style.transition = `width ${timeLimitStart}s linear`
    timeBar.style.width = _var + "%"
}

// ローマ字の表示を更新
function updateRomajiWord(_romaji) {
    let highlightedText = keyHistory;
    let remainingText = _romaji.substring(currentPosition);
    // ハイライトされたテキストと残りのテキストを表示
    romajiWord.innerHTML = `<span id="highlightedText" style="color: blue;">${highlightedText}</span>${remainingText}`;
}

function showWord() {
    romajiWord.style.display = "block";    
    nextWord.style.display = "block";
}

// ミスしたキーのハイライト
function highlightMistakeKey(_key) {
    if (!useHeighlight)
        return;
    key[_key].style.backgroundColor = `rgba(255, 0, 0, ${mistakesCount[_key] * 10 / 255})`;
    // console.log(key[_key].style.backgroundColor);
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

/*------------------------------------------------
    初期化
------------------------------------------------*/
document.addEventListener('DOMContentLoaded', function() {
    setNextGame();
    fetch ("../user_settings/user_settings.php")
        .then(response => response.json())
        .then(data => {
            // ハイライトを使用
            useHeighlight = data.missHighlight;
            // ランキングに表示するかの設定
            isDisplay = !data.privacy;
            console.log("data.privacy: " + data.privacy);
            console.log("isDisplay: " + isDisplay);
        });
});