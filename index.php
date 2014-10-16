<?
/**
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas
 * @version 0.0.7
 *
 * It is dynamic chat window.
 */

include 'includes.php';
DB::connect(); 
if (!Login::isLoggedIn())
	Logger::info(
		t("Demo account user: demo, password: demo!")); 
Login::CheckLogin();
ChatManager::checkTalker();
Req::process();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<title>Chat</title>
	</head>
<body>
<?= LoggerHtmlBlock::messages() ?>
<? if (!Login::isLoggedIn()) {
	LoginHtmlBlock::login();
} else { ?>
<div id="msgs"></div>
<p class="small">Requirements: Chrome &gt;=5</p>
<p class="meniu no_underline"> <a href="#" onclick="wm.eventChangeNicknameClicked()" tabindex="3" accesskey="n">Change <span class="akey">n</span>ickname</a><p>

<form id="form_set_nickname" onsubmit="wm.setNicknameSubmit(); return false">
	<label for="input_talker_nickname">Write your nickname</label>
	<input type="text" name="talker[nickname]" id="input_talker_nickname" tabindex="4" />
	<input type="submit" value="Set!" tabindex="5" />
</form>

<span id="talkers_list_box"></span>
<div id="chat_talks_box"></div>
<div class="clear"></div>

<form id="form_chat_box" onsubmit="wm.chatBoxSubmitted();return false">
	<input type="submit" value="Say!" id="button_say" tabindex="2" />
	<label for="chat_box_input"><span class="akey">t</span>ext:&gt;</label>
	<input type="text" name="talk[what]" id="chat_box_input" size="60" tabindex="1" accesskey="t" />
</form>

<script type="text/javascript" src="javascript/chat.js"></script>
<? } ?>

<p>2013 <a href="mailto:mzilenas@gmail.com">Marius Žilėnas</a></p>
</body>
</html>
