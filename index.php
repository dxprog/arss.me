<?php

require('lib/aal.php');

session_start();

$user = Api\User::getCurrentUser();
if (!$user) {
    $facebook = new \Facebook\Facebook([ 'appId' => FB_APP, 'secret' => FB_SECRET ]);
    $fbUserId = $facebook->getUser();
    
    if ($fbUserId === 0) {
        Lib\Display::setVariable('login_url', $facebook->getLoginUrl());
        Lib\Display::setTemplate('login');
    } else {
        $user = $facebook->api('me/');
        $user = Api\User::verifyFacebookUser($user, true);
        if ($user->id) {
            $_SESSION['user'] = $user;
            session_write_close();
        }
    }
}

if ($user) {
    $feeds = Api\Subscription::getSubscribedFeeds();
    $items = Api\Subscription::getUnreadItems();
    usort($items, function($a, $b) {
        return $a->date < $b->date ? -1 : 1;
    });

    for ($i = 0, $count = count($items); $i < $count; $i++) {
        $items[$i]->content = preg_replace('/\<!--(.*?)--\>/i', '', $items[$i]->content);
    }
    
    Lib\Display::setTemplate('default');
    Lib\Display::setVariable('feeds', json_encode($feeds));
    Lib\Display::setVariable('items', json_encode($items));
}

Lib\Display::render();