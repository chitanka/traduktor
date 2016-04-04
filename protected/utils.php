<?php

function is_developer() {
    return $_SERVER["SERVER_NAME"] == "dev.nota.app";
}

function prr($obj, $title = '') {
    echo "\n<pre>" . ($title != '' ? "<b>{$title}</b>\n" : "") . htmlspecialchars(print_r($obj, true)) . "</pre>\n";
}

function appParams() {
    return Yii::app()->params;
}

function isRegistrationOpen() {
    return appParams()['registerType'] == "OPEN";
}

function isRegistrationByInvite() {
    return appParams()['registerType'] == "INVITE";
}
