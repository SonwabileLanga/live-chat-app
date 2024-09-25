<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 5.1.3                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2024 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../config.php')) die('install/[db_update.php] config.php not exist');
require_once '../config.php';

// Finally verify the license
require_once '../class/class.jaklic.php';
$jakCheck = new JAKCheckAPI();

if (is_numeric($_POST['step']) && $_POST['step'] == 4) {

  $verify_response = $jakCheck->verify_license(false);

  if ($verify_response['status'] != true) {
    die(json_encode(array("status" => 3)));
  }

$result = $jakdb->get("departments", "title", ["id" => 1]);
  	
if ($result) {

// Check the current version
$version = $jakdb->get("settings", "used_value", ["varname" => "version"]);

// We need at least Version 4.0, older versions are not supported anymore
if ($version < "4.0") die(json_encode(array("status" => 4)));

// Ok, we are already up to date
if ($version == "5.1.3") die(json_encode(array("status" => 2)));

if ($version < "4.0.1") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
  ADD `floatcss_safari` varchar(100) COLLATE 'utf8_general_ci' NULL AFTER `floatcss`");
}

// In case it did not work:
if (!$jakdb->has("settings", ["varname" => "smtp_sender"])) {
  $jakdb->query("INSERT INTO ".JAKDB_PREFIX."settings (`varname`, `used_value`, `default_value`)
    VALUES ('smtp_sender', '".JAK_EMAIL."', '')");
}

// Update 4.0.4
if ($version < "4.0.4") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."checkstatus
  ADD `alert` tinyint(3) unsigned NOT NULL AFTER `datac`");
}

// Update 4.0.5
if ($version < "4.0.5") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
  ADD `chat_window_dir` tinyint(3) unsigned NULL DEFAULT '0' AFTER `engagecss`,
  ADD `iconadv` varchar(255) COLLATE 'utf8_general_ci' NULL DEFAULT 'far fa-comments' AFTER `engage_animation`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."user
  ADD `alwaysonline` tinyint(1) NOT NULL DEFAULT '0' AFTER `alwaysnot`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."push_notification_devices
  ADD `appname` enum('lc3','hd3') COLLATE 'utf8_general_ci' NULL AFTER `token`,
  ADD `appversion` varchar(10) COLLATE 'utf8_general_ci' NULL AFTER `appname`");

  $jakdb->query("TRUNCATE TABLE ".JAKDB_PREFIX."push_notification_devices");
}

// Update 5.0
if ($version < "5.0") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
  DROP `whatsapp_message`,
  DROP `widget`,
  DROP `hideoff`,
  DROP `buttonimg`,
  DROP `mobilebuttonimg`,
  DROP `slideimg`,
  DROP `floatpopup`,
  DROP `chat_direct`,
  DROP `whatsapp_online`,
  DROP `whatsapp_offline`,
  DROP `client_email`,
  DROP `client_semail`,
  DROP `client_phone`,
  DROP `client_sphone`,
  DROP `client_question`,
  DROP `client_squestion`,
  DROP `show_avatar`,
  DROP `floatcss`,
  DROP `floatcss_safari`,
  DROP `floatcsschat`,
  DROP `engagecss`,
  DROP `chat_window_dir`,
  DROP `btn_animation`,
  DROP `chat_animation`,
  DROP `engage_animation`,
  DROP `iconadv`,
  DROP `sucolor`,
  DROP `sutcolor`,
  DROP `theme_colour`,
  DROP `body_colour`,
  DROP `h_colour`,
  DROP `c_colour`,
  DROP `time_colour`,
  DROP `link_colour`,
  DROP `sidebar_colour`,
  DROP `t_font`,
  DROP `h_font`,
  DROP `c_font`,
  DROP `widget_whitelist`,
  ADD `hidewhenoff` tinyint(3) unsigned DEFAULT '0' AFTER `feedback`,
  ADD `avatarset` varchar(20) DEFAULT 'business' AFTER `template`,
  ADD `btn_tpl` varchar(100) DEFAULT 'icon_bottom_right.php' AFTER `avatarset`,
  ADD `start_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php' AFTER `btn_tpl`,
  ADD `chat_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php' AFTER `start_tpl`,
  ADD `contact_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php' AFTER `chat_tpl`,
  ADD `profile_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php' AFTER `contact_tpl`,
  ADD `feedback_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php' AFTER `profile_tpl`,
  ADD `updated` datetime NOT NULL DEFAULT '1980-05-06 00:00:00' AFTER `feedback_tpl`");

  $jakdb->query("CREATE TABLE ".JAKDB_PREFIX."chatsettings (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `widgetid` int(10) unsigned NOT NULL DEFAULT '0',
    `template` varchar(20) DEFAULT 'business',
    `formtype` varchar(20) DEFAULT NULL,
    `lang` varchar(2) DEFAULT NULL,
    `settname` varchar(100) DEFAULT NULL,
    `settvalue` text,
    `updated` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    PRIMARY KEY (`id`),
    KEY `widgetid` (`widgetid`, `template`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

  $jakdb->query("CREATE TABLE ".JAKDB_PREFIX."chatcustomfields (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `convid` int(10) unsigned NOT NULL DEFAULT '0',
    `contactid` int(10) unsigned NOT NULL DEFAULT '0',
    `name` varchar(100) NULL DEFAULT NULL,
    `settname` varchar(100) DEFAULT NULL,
    `settvalue` text,
    `updated` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    PRIMARY KEY (`id`),
    KEY `convid` (`convid`,`contactid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."sessions
    ADD `avatarset` varchar(20) COLLATE 'utf8_general_ci' DEFAULT 'business' AFTER `template`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."buttonstats
  DROP `proactive`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."sessions
  ADD `uniqueid` varchar(20) COLLATE 'utf8_general_ci' NULL DEFAULT NULL AFTER `id`");

  // We need a few pre defined answers
  $jakdb->query("INSERT INTO ".JAKDB_PREFIX."answers (`department`, `lang`, `title`, `message`, `fireup`, `msgtype`, `created`)
  VALUES ('0', 'en', 'Select Operator', 'Please select an operator of your choice and add your name and message to start a conversation.', '15', '14', NOW()),
  (0, 'en', 'Expired Soft', 'The chat has been ended due the inactivity, please type a message to restart again.', 15, 15, NOW()),
  (0, 'en', 'Transfer Message', 'We have transferred your conversation to %operator%, please hold.', 15, 16, NOW())");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."responses
  ADD `short_code` varchar(200) COLLATE 'utf8_general_ci' NULL AFTER `title`");

  $jakdb->query("DELETE FROM ".JAKDB_PREFIX."settings
    WHERE ((`varname` = 'captcha'))");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
  ADD `onlymembers` tinyint(3) unsigned NULL DEFAULT '0' AFTER `hidewhenoff`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."buttonstats
  ADD `crossurl` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `firstreferrer`");
}

if ($version < "5.0.1") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."sessions
  ADD `widgetid` int(10) unsigned NOT NULL DEFAULT '1' AFTER `id`");
}

// Update to 5.0.7
if ($version1 < "5.0.7") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."transcript
    ADD `standardmsg` int(10) unsigned NOT NULL AFTER `convid`");
}

// Update to 5.1
if ($version < "5.1") {

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."answers
CHANGE `lang` `lang` varchar(5) COLLATE 'utf8_general_ci' NULL AFTER `department`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatsettings
CHANGE `lang` `lang` varchar(5) COLLATE 'utf8_general_ci' NULL AFTER `formtype`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."bot_question
CHANGE `lang` `lang` varchar(5) COLLATE 'utf8_general_ci' NULL AFTER `depid`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
CHANGE `lang` `lang` varchar(5) COLLATE 'utf8_general_ci' NULL AFTER `opid`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."groupchat
CHANGE `lang` `lang` varchar(5) COLLATE 'utf8_general_ci' NULL AFTER `maxclients`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."sessions
CHANGE `lang` `lang` varchar(5) COLLATE 'utf8_general_ci' NULL DEFAULT 'en' AFTER `longitude`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."groupchat
    ADD `chatstyle` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `lang`,
    ADD `bgimage` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `chatstyle`");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
    ADD `chatgpt` tinyint(3) unsigned NULL DEFAULT '0' AFTER `onlymembers`,
    ADD `chatgpt_helpful` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `chatgpt`");

  $jakdb->query("INSERT INTO ".JAKDB_PREFIX."settings (`id`, `varname`, `used_value`, `default_value`)
    VALUES (NULL, 'openai_chatgpt', '0', '0'),
           (NULL, 'openai_apikey', '', '')");

  $jakdb->query("CREATE TABLE ".JAKDB_PREFIX."bot_chatgpt (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `widgetids` varchar(100) DEFAULT '0',
    `depid` int(10) unsigned NOT NULL DEFAULT '0',
    `lang` varchar(5) DEFAULT NULL,
    `question` text,
    `answer` text,
    `updated` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."bot_chatgpt
    ADD FULLTEXT `question_answer` (`question`, `answer`)");

}

// Version 5.1.1
if ($version < "5.1.1") {
  $jakdb->query("ALTER TABLE ".JAKDB_PREFIX."chatwidget
    CHANGE `chatgpt_helpful` `chatgpt_helpful` text COLLATE 'utf8_general_ci' NULL AFTER `chatgpt`");

  $jakdb->query("CREATE TABLE ".JAKDB_PREFIX."chat_ai_translations (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `chatid` int(10) unsigned NOT NULL DEFAULT '0',
    `answerid` int(10) unsigned NOT NULL DEFAULT '0',
    `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
    `original_lang` text,
    `translated_lang` text,
    `lang` varchar(5) DEFAULT NULL,
    `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
    PRIMARY KEY (`id`),
    KEY `chatid` (`chatid`,`answerid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
  
}

// Version 5.1.2
if ($version < "5.1.2") {
  $jakdb->query("INSERT INTO ".JAKDB_PREFIX."settings (`id`, `varname`, `used_value`, `default_value`)
    VALUES (NULL, 'o_uname', '', 'jAkWEb')");
}

// Version 5.1.3
if ($version < "5.1.3") {
$jakdb->query("INSERT INTO ".JAKDB_PREFIX."settings (`id`, `varname`, `used_value`, `default_value`)
    VALUES (NULL, 'chatgpt_version', 'gpt-3.5-turbo', 'gpt-3.5-turbo')");
}

// update time so css and javascript will be loaded fresh
$jakdb->update("settings", ["used_value" => time()], ["varname" => "updated"]);
// update version
$jakdb->update("settings", ["used_value" => "5.1.3"], ["varname" => "version"]);

// Now let us delete all cache files
$cacheallfiles = '../'.JAK_CACHE_DIRECTORY.'/';
$msfi = glob($cacheallfiles."*.php");
if ($msfi) foreach ($msfi as $filen) {
    if (file_exists($filen)) unlink($filen);
}
	
die(json_encode(array("status" => 1)));

}

} else {
	die(json_encode(array("status" => 0)));
}
?>