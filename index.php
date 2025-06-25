<?php
$botToken = getenv('BOT_TOKEN');
$logFile = __DIR__ . '/data/error.log';

if (!file_exists($logFile)) {
    @mkdir(__DIR__ . '/data', 0777, true);
    @touch($logFile);
}

function logData($data) {
    file_put_contents(__DIR__ . '/data/error.log', date('[Y-m-d H:i:s] ') . $data . "\n", FILE_APPEND);
}

// Handle webhook setup
if (isset($_GET['setup_webhook']) && isset($_GET['token']) && $_GET['token'] === getenv('BOT_TOKEN')) {
    $domain = 'https://' . $_SERVER['HTTP_HOST'] . '/index.php';
    $result = file_get_contents("https://api.telegram.org/bot{$botToken}/setWebhook?url=" . urlencode($domain));
    logData("Webhook Setup Response: $result");
    echo "Webhook setup: $result";
    exit;
}

$input = file_get_contents('php://input');
if (!$input) exit;

$update = json_decode($input, true);
logData("Received: " . json_encode($update));

$chat_id = $update['message']['chat']['id'] ?? null;
$text = $update['message']['text'] ?? '';

$photo1 = 'https://postimg.cc/Mf7r7wpT';
$photo2 = 'https://postimg.cc/5H87d18m';
$photo3 = 'https://postimg.cc/5H87d18m';

$text1 = "🙂 , Welcome\n\n📲 Our bot is based on a neural network from OpenAI. He can predict the location with 99% probability";
$text2 = "Rules are simple:\n\n1. Click on Register🕹\n2.SIGN UP\n3. Make at least a FIRST DEPOSIT ₹500-₹1500\n4.For Any support message @jassss_07";
$text3 = "SERVER FOUND 🤖 24.06.2025 08:49:42 (GMT+3)";
$text3b = "LOADING...";
$text3c = "SIGNAL SAVED ✅";
$text4 = "SIGNAL ✅️\n\nSTATUS- ✅️\n\nUSER REGISTERED ❌️\n\n1.You're Not REGISTERED❌️ FIRST REGISTER BY CLICK ON REGISTER🕹\n2. Sign Up\n3.Make Atleast First Deposit ₹500-1500\n4.ACCESS OF BOT✅️";

$link = "https://click.traffprogo7.com/KTDUy9a0";

function send($method, $data) {
    global $botToken;
    file_get_contents("https://api.telegram.org/bot{$botToken}/{$method}?" . http_build_query($data));
}

function sendPhoto($chat_id, $photo, $caption, $buttons = null) {
    $data = [
        'chat_id' => $chat_id,
        'photo' => $photo,
        'caption' => $caption,
        'parse_mode' => 'HTML'
    ];
    if ($buttons) {
        $data['reply_markup'] = json_encode(['inline_keyboard' => $buttons]);
    }
    send('sendPhoto', $data);
}

function sendMessage($chat_id, $text, $buttons = null) {
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    if ($buttons) {
        $data['reply_markup'] = json_encode(['inline_keyboard' => $buttons]);
    }
    send('sendMessage', $data);
}

if ($chat_id && $text === "/start") {
    $buttons = [
        [['text' => 'Instructions⚙️', 'callback_data' => 'instructions']],
        [['text' => 'Start✅️', 'callback_data' => 'start']]
    ];
    sendPhoto($chat_id, $photo1, $text1, $buttons);
} elseif (isset($update['callback_query'])) {
    $cb = $update['callback_query'];
    $cid = $cb['message']['chat']['id'];
    $data = $cb['data'];

    if ($data === 'instructions') {
        $buttons = [
            [['text' => 'REGISTER🕹', 'url' => $link]],
            [['text' => 'Start✅️', 'callback_data' => 'start']]
        ];
        sendPhoto($cid, $photo2, $text2, $buttons);
    } elseif ($data === 'start') {
        sendMessage($cid, $text3);
        sleep(1);
        sendMessage($cid, $text3b);
        sleep(2);
        $buttons = [[['text' => 'GET SIGNAL🚀', 'callback_data' => 'signal']]];
        sendMessage($cid, $text3c, $buttons);
    } elseif ($data === 'signal') {
        $buttons = [[['text' => 'REGISTER🕹', 'url' => $link]]];
        sendPhoto($cid, $photo3, $text4, $buttons);
    }
} elseif ($chat_id && $text) {
    $buttons = [[['text' => 'REGISTER🕹', 'url' => $link]]];
    sendPhoto($chat_id, $photo3, $text2, $buttons);
}
?>
