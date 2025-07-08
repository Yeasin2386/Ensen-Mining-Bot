<?php

// আপনার টেলিগ্রাম বট API টোকেন
// **গুরুত্বপূর্ণ: 'YOUR_BOT_TOKEN' এর বদলে আপনার আসল টোকেনটি বসান।**
define('BOT_TOKEN', 'YOUR_BOT_TOKEN'); 

// টেলিগ্রাম API এর বেস URL
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// টেলিগ্রাম থেকে পাঠানো আপডেট ডেটা গ্রহণ করা হচ্ছে
$update = json_decode(file_get_contents('php://input'), true);

// যদি প্রাপ্ত আপডেটে কোনো মেসেজ থাকে
if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id']; // মেসেজ যে চ্যাট থেকে এসেছে তার আইডি
    $text = $message['text'];           // ব্যবহারকারীর পাঠানো মেসেজ

    // লোগিং এর জন্য (বট চালু আছে কিনা বুঝতে)
    // এটি Render এর লগসে দেখা যাবে
    error_log("Received message from chat ID: " . $chat_id . " - Text: " . $text);

    // ব্যবহারকারীর মেসেজের উপর ভিত্তি করে বিভিন্ন প্রতিক্রিয়া
    switch ($text) {
        case '/start':
            // যখন ব্যবহারকারী /start কমান্ড পাঠাবে
            $response_text = "স্বাগতম! 👋 আমি একটি সাধারণ টেস্টিং বট। আমি এখন অনলাইনে আছি এবং কাজ করছি। আপনি /hello লিখে দেখতে পারেন।";
            sendMessage($chat_id, $response_text);
            break;
        case '/hello':
            // যখন ব্যবহারকারী /hello কমান্ড পাঠাবে
            $response_text = "হ্যালো! 😊 আমি ঠিকঠাক কাজ করছি। আপনি যা লিখেছেন তা হলো: '" . $text . "'।";
            sendMessage($chat_id, $response_text);
            break;
        default:
            // অন্য যেকোনো মেসেজের জন্য ডিফল্ট প্রতিক্রিয়া
            $response_text = "আপনি বলেছেন: '" . $text . "'। আমি এই কমান্ডটি বুঝি না। তবে, আমি এখনও অনলাইন আছি এবং আপনার মেসেজ পাচ্ছি! 🎉";
            sendMessage($chat_id, $response_text);
            break;
    }
}

/**
 * এই ফাংশনটি একটি নির্দিষ্ট চ্যাট আইডিতে মেসেজ পাঠানোর জন্য ব্যবহৃত হয়।
 * @param int $chat_id মেসেজ পাঠানোর জন্য চ্যাট আইডি।
 * @param string $text যে মেসেজটি পাঠাতে হবে।
 */
function sendMessage($chat_id, $text) {
    $parameters = array(
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML' // HTML ট্যাগ ব্যবহার করার অনুমতি দেয়
    );
    
    $url = API_URL . 'sendMessage?' . http_build_query($parameters);
    
    // cURL ব্যবহার করে HTTP GET রিকোয়েস্ট পাঠানো হয়েছে।
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $response = curl_exec($ch);
    curl_close($ch);
    
    // মেসেজ পাঠানোর পর লোগিং (ডিবাগিং এর জন্য)
    error_log("Sent message to chat ID: " . $chat_id . " - Text: " . $text);
    
    return $response; 
}

?>