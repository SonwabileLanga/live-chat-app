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

// Change for 3.0.3
use JAKWEB\JAKsql;

if (!file_exists('../include/db.php')) die('[install.php] include/db.php not exist');
require_once '../include/db.php';

/* NO CHANGES FROM HERE */
if (!file_exists('../class/class.jaklic.php')) die('It looks like the boat has been reported as missing.');

// Get the ls DB class
require_once '../class/class.db.php';

// Fresh installation
$fresh_install = false;

if (is_numeric($_POST['step']) && $_POST['step'] == 3) {

  $dsn = JAKDB_DBTYPE.':dbname='.JAKDB_NAME.';host='.JAKDB_HOST;

  try {
    $dbh = new PDO($dsn, JAKDB_USER, JAKDB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      $check_db = false;
      $db_error_msg = $e->getMessage();
  }

  try {
    $dbh->query("SELECT title FROM ".JAKDB_PREFIX."departments WHERE id = 1 LIMIT 1");
  } catch (Exception $e) {
    // We got an exception == table not found
    $fresh_install = true;
  }
    
if ($fresh_install) {

  // Database connection
  $jakdb = new JAKsql([
    // required
    'database_type' => JAKDB_DBTYPE,
    'database_name' => JAKDB_NAME,
    'server' => JAKDB_HOST,
    'username' => JAKDB_USER,
    'password' => JAKDB_PASS,
    'charset' => 'utf8',
    'port' => JAKDB_PORT,
    'prefix' => JAKDB_PREFIX,
 
    // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
    'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL]
    ]);

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."answers (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `department` int(10) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `fireup` smallint(5) unsigned NOT NULL DEFAULT '60',
  `msgtype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=standard,2=welcome,3=closed,4=expired,5=firstmsg',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `depid` (`department`,`lang`,`fireup`,`msgtype`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");

$jakdb->query("INSERT INTO ".JAKDB_PREFIX."answers (`id`, `department`, `lang`, `title`, `message`, `fireup`, `msgtype`, `created`) VALUES
(1, 0, 'en', 'Enters Chat', '%operator% enters the chat.', 15, 2, NOW()),
(2, 0, 'en', 'Expired', 'This chat has been ended for good. Please start a new one or use your personal restore code.', 15, 4, NOW()),
(3, 0, 'en', 'Ended', '%client% has ended the conversation', 15, 3, NOW()),
(4, 0, 'en', 'Welcome', 'Welcome %client%, a representative will be with you shortly.', 15, 5, NOW()),
(5, 0, 'en', 'Leave', 'has left the conversation.', 15, 6, NOW()),
(6, 0, 'en', 'Start Page', 'Please insert your name to begin, a representative will be with you shortly.', 15, 7, NOW()),
(7, 0, 'en', 'Contact Page', 'None of our representatives are available right now, although you are welcome to leave a message!', 15, 8, NOW()),
(8, 0, 'en', 'Feedback Page', 'We would appreciate your feedback to improve our service.', 15, 9, NOW()),
(9, 0, 'en', 'Quickstart Page', 'Please type a message and hit enter to start the conversation.', 15, 10, NOW()),
(10, 0, 'en', 'Group Chat Welcome Message', 'Welcome to our weekly support session, sharing experience and feedback.', 0, 11, NOW()),
(11, 0, 'en', 'Group Chat Offline Message', 'The public chat is offline at this moment, please try again later.', 15, 12, NOW()),
(12, 0, 'en', 'Group Chat Full Message', 'The public chat is full, please try again later.', 15, 13, NOW()),
(NULL, 0, 'en', 'Select Operator', 'Please select an operator of your choice and add your name and message to start a conversation.', 15, 14, NOW()),
(NULL, 0, 'en', 'Expired Soft', 'The chat has been ended due the inactivity, please type a message to restart again.', 15, 15, NOW()),
(NULL, 0, 'en', 'Transfer Message', 'We have transferred your conversation to %operator%, please hold.', 15, 16, NOW()),
(NULL, 0, 'en', 'WhatsApp Online', 'Please click on a operator below to connect via WhatsApp and get help immediately.', 15, 26, NOW()),
(NULL, 0, 'en', 'WhatsApp Offline', 'We are currently offline however please check below for available operators in WhatsApp, we try to help you as soon as possible.', 15, 27, NOW())");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."autoproactive (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `path` varchar(200) NULL DEFAULT NULL,
  `title` varchar(255) NULL DEFAULT NULL,
  `imgpath` varchar(255) NULL DEFAULT NULL,
  `message` varchar(255) NULL DEFAULT NULL,
  `btn_confirm` VARCHAR(50) NULL DEFAULT NULL,
  `btn_cancel` VARCHAR(50) NULL DEFAULT NULL,
  `showalert` smallint(1) unsigned NOT NULL DEFAULT '1',
  `soundalert` VARCHAR(100) NULL DEFAULT NULL,
  `timeonsite` smallint(3) unsigned NOT NULL DEFAULT '2',
  `visitedsites` smallint(2) unsigned NOT NULL DEFAULT '1',
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."urlblacklist (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `path` varchar(200) NULL DEFAULT NULL,
  `title` varchar(255) NULL DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."buttonstats (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `depid` int(10) unsigned NOT NULL DEFAULT '0',
  `opid` int(10) unsigned NOT NULL DEFAULT '0',
  `referrer` varchar(255) DEFAULT NULL,
  `firstreferrer` varchar(255) DEFAULT NULL,
  `crossurl` varchar(255) DEFAULT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `hits` int(10) NOT NULL DEFAULT '0',
  `ip` char(45) NOT NULL DEFAULT '0',
  `country` varchar(64) DEFAULT NULL,
  `countrycode` CHAR(2) NOT NULL DEFAULT 'xx',
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `readtime` smallint(1) NOT NULL DEFAULT '0',
  `session` varchar(64) DEFAULT NULL,
  `lasttime` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `depid` (`depid`),
  KEY `session` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."bot_question (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `widgetids` varchar(100) DEFAULT '0',
  `depid` int(10) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) DEFAULT NULL,
  `question` text,
  `answer` text,
  `updated` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `widgetids` (`widgetids`, `depid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

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

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."chatwidget (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `depid` varchar(50) NOT NULL DEFAULT '0',
  `opid` int(10) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) DEFAULT NULL,
  `dsgvo` text,
  `redirect_url` varchar(200) DEFAULT NULL,
  `redirect_active` tinyint(3) unsigned DEFAULT '0',
  `redirect_after` tinyint(3) unsigned DEFAULT '8',
  `feedback` tinyint(3) unsigned DEFAULT '0',
  `hidewhenoff` tinyint(3) unsigned DEFAULT '0',
  `onlymembers` tinyint(3) unsigned DEFAULT '0',
  `chatgpt` tinyint(3) unsigned DEFAULT '0',
  `chatgpt_helpful` text DEFAULT NULL,
  `template` varchar(20) DEFAULT 'business',
  `avatarset` varchar(20) DEFAULT 'business',
  `btn_tpl` varchar(100) DEFAULT 'icon_bottom_right.php',
  `start_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php',
  `chat_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php',
  `contact_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php',
  `profile_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php',
  `feedback_tpl` varchar(100) DEFAULT 'small_big_bottom_right.php',
  `updated` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `depid` (`depid`, `opid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$jakdb->query("INSERT INTO ".JAKDB_PREFIX."chatwidget (`id`, `title`, `depid`, `opid`, `lang`, `updated`, `created`) VALUES
(1, 'Live Support Chat',  0,  0,  'en', NOW(), NOW())");

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

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."chatsettings (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `widgetid` int(10) unsigned NOT NULL DEFAULT '0',
  `template` varchar(20) DEFAULT 'business',
  `formtype` varchar(20) DEFAULT NULL,
  `lang` varchar(5) DEFAULT NULL,
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

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."clientcontact (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sessionid` int(10) unsigned NOT NULL DEFAULT '0',
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `operatorname` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `sent` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."contacts (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `depid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `message` text,
  `ip` char(45) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `countrycode` varchar(2) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `reply` smallint(1) unsigned NOT NULL DEFAULT '0',
  `answered` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `sent` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `depid` (`depid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."contactsreply (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contactid` int(10) unsigned NOT NULL DEFAULT '0',
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `operatorname` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `sent` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `contactid` (`contactid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."departments (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `email` varchar(255) DEFAULT NULL,
  `faq_url` text,
  `active` smallint(1) unsigned NOT NULL DEFAULT '1',
  `dorder` smallint(2) unsigned NOT NULL DEFAULT '1',
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");

$jakdb->query("INSERT INTO ".JAKDB_PREFIX."departments (`id`, `title`, `description`, `active`, `dorder`, `time`) VALUES
(1, 'Live Support', 'Edit this department to your needs...', 1, 1, NOW())");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."files (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `path` text,
  `name` varchar(200) NULL DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."groupchat (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `password` varchar(20) NULL DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text NULL DEFAULT NULL,
  `opids` varchar(10) DEFAULT '0',
  `maxclients` tinyint(3) unsigned NOT NULL DEFAULT '20',
  `lang` varchar(5) DEFAULT NULL,
  `chatstyle` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bgimage` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `buttonimg` varchar(100) NOT NULL,
  `floatpopup` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `floatcss` varchar(100) DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `opids` (`opids`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$jakdb->query("INSERT INTO ".JAKDB_PREFIX."groupchat (`id`, `title`, `opids`, `maxclients`, `lang`, `buttonimg`, `floatpopup`, `floatcss`, `active`, `created`) VALUES
(1, 'Weekly Support', '0', 10, 'en', 'colour_on.png', 0, 'bottom:20px;left:20px', 0, NOW())");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."groupchatmsg (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `groupchatid` int(10) NOT NULL DEFAULT '0',
  `chathistory` mediumtext,
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `groupchatid` (`groupchatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."groupchatuser (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `groupchatid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) NULL DEFAULT NULL,
  `usr_avatar` varchar(255) NULL DEFAULT NULL,
  `statusc` int(10) unsigned NOT NULL DEFAULT '0',
  `lastmsg` int(10) unsigned NOT NULL DEFAULT '0',
  `banned` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ip` char(45) NOT NULL DEFAULT '0',
  `isop` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `session` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `groupchatid` (`groupchatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

// We add the new whatslog table
$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."whatslog (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `guestid` varchar(200) NULL DEFAULT NULL,
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `clientid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `whatsid` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `fromwhere` varchar(255) DEFAULT NULL,
  `ip` char(45) NOT NULL DEFAULT '0',
  `country` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `countrycode` varchar(2) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `usragent` varchar(255) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."operatorchat (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromid` int(10) NOT NULL DEFAULT '0',
  `toid` int(10) NOT NULL DEFAULT '0',
  `message` text NULL DEFAULT NULL,
  `sent` int(10) NOT NULL DEFAULT '0',
  `received` smallint(1) unsigned NOT NULL DEFAULT '0',
  `msgpublic` smallint(1) unsigned NOT NULL DEFAULT '0',
  `system_message` varchar(3) DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."responses (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `department` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(200) NULL DEFAULT NULL,
  `short_code` varchar(200) NULL DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");

$jakdb->query("INSERT INTO ".JAKDB_PREFIX."responses (`id`, `title`, `short_code`, `message`) VALUES
(1, 'Assist Today', '/assist', 'How can I assist you today?')");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."sessions (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `widgetid` int(10) unsigned NOT NULL DEFAULT '1',
  `uniqueid` varchar(20) NULL DEFAULT NULL,
  `userid` varchar(200) NULL DEFAULT NULL,
  `department` int(10) unsigned NOT NULL DEFAULT '0',
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `operatorname` varchar(255) NULL DEFAULT NULL,
  `template` varchar(20) NULL DEFAULT 'business',
  `avatarset` varchar(20) NULL DEFAULT 'business',
  `usr_avatar` varchar(255) NULL DEFAULT NULL,
  `name` varchar(100) NULL DEFAULT NULL,
  `email` varchar(100) NULL DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `countrycode` varchar(2) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `lang` varchar(5) DEFAULT 'en',
  `notes` text,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fcontact` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `initiated` int(10) unsigned NOT NULL DEFAULT '0',
  `ended` int(10) unsigned NOT NULL DEFAULT '0',
  `deniedoid` int(10) unsigned NOT NULL DEFAULT '0',
  `session` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `operatorid` (`operatorid`),
  KEY `session` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."settings (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `varname` varchar(100) DEFAULT NULL,
  `used_value` text,
  `default_value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$jakdb->query("INSERT INTO ".JAKDB_PREFIX."settings (`id`, `varname`, `used_value`, `default_value`) VALUES
(NULL, 'allowedo_files', '.zip,.rar,.jpg,.jpeg,.png,.gif', '.zip,.rar,.jpg,.jpeg,.png,.gif'),
(NULL, 'allowed_files', '.zip,.rar,.jpg,.jpeg,.png,.gif', '.zip,.rar,.jpg,.jpeg,.png,.gif'),
(NULL, 'validtill', 0, 0),
(NULL, 'client_expired', '600', '600'),
(NULL, 'client_left', '300', '300'),
(NULL, 'crating', '1', '0'),
(NULL, 'dateformat', 'd.m.Y', 'd.m.Y'),
(NULL, 'email', '', '@lc3jak'),
(NULL, 'emailcc', '', '@jakcc'),
(NULL, 'email_block', '', NULL),
(NULL, 'facebook', '', ''),
(NULL, 'facebook_big', '', ''),
(NULL, 'ip_block', '', NULL),
(NULL, 'lang', 'en', 'en'),
(NULL, 'live_online_status', '0', '0'),
(NULL, 'chat_upload_standard', '0', '0'),
(NULL, 'msg_tone', 'new_message', 'new_message'),
(NULL, 'openop', '1', '1'),
(NULL, 'o_number', '', 'JaKwEb'),
(NULL, 'o_uname', '', 'jAkWEb'),
(NULL, 'pro_alert', '1', '1'),
(NULL, 'ring_tone', 'ring', 'ring'),
(NULL, 'send_tscript', '1', '1'),
(NULL, 'show_ips', '1', '1'),
(NULL, 'smtp_sender', '', ''),
(NULL, 'smtphost', '', ''),
(NULL, 'smtppassword', '', ''),
(NULL, 'smtpport', '25', '25'),
(NULL, 'smtpusername', '', ''),
(NULL, 'smtp_alive', '0', '0'),
(NULL, 'smtp_auth', '0', '0'),
(NULL, 'smtp_mail', '0', '0'),
(NULL, 'smtp_prefix', '', ''),
(NULL, 'timeformat', 'g:i a', 'g:i a'),
(NULL, 'timezoneserver', 'Europe/Zurich', 'Europe/Zurich'),
(NULL, 'title', 'Live Chat 3', 'Live Chat 3'),
(NULL, 'twilio_nexmo', '0', '1'),
(NULL, 'twitter', '', ''),
(NULL, 'twitter_big', '', ''),
(NULL, 'tw_msg', 'A customer is requesting attention.', 'A customer is requesting attention.'),
(NULL, 'tw_phone', '', ''),
(NULL, 'tw_sid', '', ''),
(NULL, 'tw_token', '', ''),
(NULL, 'updated', ".time().", '1475494685'),
(NULL, 'useravatheight', '113', '113'),
(NULL, 'useravatwidth', '150', '150'),
(NULL, 'version', '5.1.3', '5.1.3'),
(NULL, 'holiday_mode', '0', '0'),
(NULL, 'push_reminder', '120', '120'),
(NULL, 'native_app_token', '', 'jakweb_app'),
(NULL, 'native_app_key', '', 'jakweb_app'),
(NULL, 'client_push_not', '1', '1'),
(NULL, 'engage_sound', 'sound/new_message3', 'sound/new_message3'),
(NULL, 'engage_icon', 'fa-bells', 'fa-bells'),
(NULL, 'client_sound', 'sound/hello', 'sound/hello'),
(NULL, 'proactive_time', '3', '3'),
(NULL, 'openai_chatgpt', '0', '0'),
(NULL, 'openai_apikey', '', ''),
(NULL, 'chatgpt_version', 'gpt-3.5-turbo', 'gpt-3.5-turbo')");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."transcript (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL DEFAULT NULL,
  `message` varchar(2000) NULL DEFAULT NULL,
  `user` varchar(100) NULL DEFAULT NULL,
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `convid` int(10) unsigned NOT NULL DEFAULT '0',
  `standardmsg` int(10) unsigned NOT NULL DEFAULT '0',
  `quoted` int(10) unsigned NOT NULL DEFAULT '0',
  `replied` int(10) unsigned NOT NULL DEFAULT '0',
  `starred` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `editoid` int(10) unsigned NOT NULL DEFAULT '0',
  `edited` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `sentstatus` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `class` varchar(20) NULL DEFAULT NULL,
  `plevel` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `convid` (`convid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."user (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `departments` varchar(100) DEFAULT '0',
  `available` smallint(1) unsigned NOT NULL DEFAULT '0',
  `busy` smallint(1) unsigned NOT NULL DEFAULT '0',
  `hours_array` TEXT NULL,
  `phonenumber` varchar(255) DEFAULT NULL,
  `whatsappnumber` varchar(255) DEFAULT NULL,
  `pusho_tok` VARCHAR(50) DEFAULT NULL,
  `pusho_key` VARCHAR(50) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` char(64) NULL DEFAULT NULL,
  `idhash` varchar(32) DEFAULT NULL,
  `session` varchar(64) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `picture` varchar(100) NOT NULL DEFAULT '/standard.jpg',
  `aboutme` TEXT NULL,
  `language` varchar(10) DEFAULT NULL,
  `invitationmsg` varchar(255) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `responses` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `files` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `useronlinelist` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `operatorchat` tinyint(1) NOT NULL DEFAULT '0',
  `operatorchatpublic` tinyint(1) NOT NULL DEFAULT '1',
  `operatorlist` tinyint(1) NOT NULL DEFAULT '0',
  `transferc` tinyint(1) NOT NULL DEFAULT '1',
  `chat_latency` smallint(4) UNSIGNED NOT NULL DEFAULT '3000',
  `push_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `sound` tinyint(1) NOT NULL DEFAULT '1',
  `ringing` tinyint(2) NOT NULL DEFAULT '3',
  `alwaysnot` tinyint(1) NOT NULL DEFAULT '0',
  `alwaysonline` tinyint(1) NOT NULL DEFAULT '0',
  `emailnot` tinyint(1) NOT NULL DEFAULT '0',
  `navsidebar` tinyint(1) NOT NULL DEFAULT '1',
  `themecolour` varchar(10) NOT NULL DEFAULT 'blue',
  `access` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `permissions` varchar(512) DEFAULT NULL,
  `forgot` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."user_stats (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `vote` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` text,
  `support_time` int(10) unsigned NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."checkstatus (
  `convid` int(10) unsigned NOT NULL,
  `depid` int(10) unsigned NOT NULL DEFAULT '0',
  `department` varchar(100) DEFAULT NULL,
  `operatorid` int(10) unsigned NOT NULL DEFAULT '0',
  `operator` varchar(100) DEFAULT NULL,
  `pusho` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `newc` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `newo` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `files` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knockknock` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `msgdel` int(10) unsigned NOT NULL DEFAULT '0',
  `msgedit` int(10) unsigned NOT NULL DEFAULT '0',
  `typec` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `typeo` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `transferoid` int(10) unsigned NOT NULL DEFAULT '0',
  `transferid` int(10) unsigned NOT NULL DEFAULT '0',
  `denied` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hide` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `datac` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alert` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `statusc` int(10) unsigned NOT NULL DEFAULT '0',
  `statuso` int(10) unsigned NOT NULL DEFAULT '0',
  `initiated` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `convid` (`convid`),
  KEY `denied` (`denied`,`hide`,`statusc`,`statuso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."transfer (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `convid` int(10) unsigned NOT NULL DEFAULT '0',
  `fromoid` int(10) unsigned NOT NULL DEFAULT '0',
  `fromname` varchar(100) DEFAULT NULL,
  `tooid` int(10) unsigned NOT NULL DEFAULT '0',
  `toname` varchar(100) DEFAULT NULL,
  `message` text,
  `used` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `convid` (`convid`,`tooid`,`used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$jakdb->query("CREATE TABLE ".JAKDB_PREFIX."push_notification_devices (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `ostype` enum('ios','android') NOT NULL DEFAULT 'ios',
  `token` varchar(255) DEFAULT NULL,
  `appname` enum('lc3','hd3') DEFAULT NULL,
  `appversion` varchar(10) DEFAULT NULL,
  `lastedit` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  `created` datetime NOT NULL DEFAULT '1980-05-06 00:00:00',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`ostype`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

// Now let us delete all cache files
$cacheallfiles = '../'.JAK_CACHE_DIRECTORY.'/';
$msfi = glob($cacheallfiles."*.php");
if ($msfi) foreach ($msfi as $filen) {
    if (file_exists($filen)) unlink($filen);
}
  
  die(json_encode(array("status" => 1)));

} else {
  die(json_encode(array("status" => 2)));
}

} else {
  die(json_encode(array("status" => 0)));
}
?>