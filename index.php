<?php

// Replace with your actual bot token from BotFather
// When hosting on Render.com, this line will fetch the token from environment variables.
define('BOT_TOKEN', getenv('BOT_TOKEN'));
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// Get the update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = $message['text'];

    // Handle only the /start command
    if ($text == '/start') {
        sendStartMessage($chat_id);
    }
}

// Function to send the start message with image and buttons
function sendStartMessage($chat_id) {
    // Caption text for the image
    $caption = "🚨 নতুন অ্যাকাউন্ট তৈরি করতে মাত্র ৳৫৫ টাকা লাগে! 💸\n\n"
             . "আপনার প্রতিটি সফল রেফারেলে পাবেন ৳২৫ টাকা বোনাস! 🚀\n\n"
             . "দেরি না করে এখনই শুরু করুন এবং আয় করা শুরু করুন! 🎉";

    // Create inline keyboard buttons
    $inline_keyboard = [
        [
            ['text' => '👤 অ্যাকাউন্ট তৈরি করুন', 'url' => 'https://example.com/account_create'], // Change this to your account creation link
            ['text' => '💡 সাপোর্ট', 'url' => 'https://t.me/your_support_channel'], // Change this to your support channel link
        ]
    ];

    $reply_markup = [
        'inline_keyboard' => $inline_keyboard
    ];

    $photo_path = 'referral_image.jpg'; // Path to your image file

    if (file_exists($photo_path)) {
        // Send message with photo
        $url = API_URL . 'sendPhoto';
        $post_fields = [
            'chat_id' => $chat_id,
            'photo' => new CURLFile(realpath($photo_path)),
            'caption' => $caption,
            'reply_markup' => json_encode($reply_markup),
            'parse_mode' => 'HTML',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        curl_close($ch);
        // For debugging, uncomment the line below:
        // error_log("sendPhoto output: " . $output); 

    } else {
        // Fallback: if image not found, send only text
        $url = API_URL . 'sendMessage';
        $data = [
            'chat_id' => $chat_id,
            'text' => "ছবি লোড করা যায়নি। " . $caption,
            'reply_markup' => json_encode($reply_markup),
            'parse_mode' => 'HTML',
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context  = stream_context_create($options);
        file_get_contents($url, false, $context);
        // For debugging, uncomment the line below:
        // error_log("sendMessage (fallback) output: " . $output);
    }
}

?>