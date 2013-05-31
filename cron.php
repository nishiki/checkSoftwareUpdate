<?php

	include('config.php');
	include('class.software.php');

	$message = null;

	$mysql = new mysqli($db_server, $db_user, $db_passwd, $db_name);
	
	$software = new Software($mysql);

	foreach ($software->getIdList() as $id) {
		$soft = new Software($mysql, $id);
		if ($soft->checkVersion()) {
			$message .= $soft->getName().' ---> new version: '.$soft->getVersion().' ---> old version: '.$soft->getPreviewVersion()."\n";
		}
		#$soft->close();
	}
	
	// Send notification
	if (!is_null($message)) {
		if ($mail_send) {
			foreach ($mail_senders as $to)
				mail($to, $mail_subject, $message);
		}

	}

	$mysql->close();

