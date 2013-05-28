<?php
	include('config.php');
	include('class.software.php');

	$to = 'adrien.waksberg@believedigital.com';
	$subject = '[UPDATER] new version';
	$message = "";

	$mysql = new mysqli($db_server, $db_user, $db_passwd, $db_name);
	
	$software = new Software($mysql);

	foreach ($software->getListId() as $id) {
		$soft = new Software($mysql, $id);
		if ($soft->checkVersion()) {
			$message += $soft->getName().' ---> new version: '.$soft->getVersion().' ---> old version: '.$soft->getPreviewVersion()."\n";
		}
	}

	#if (!is_null($message))
	#	mail($to, $subject, $message);

	$mysql->close();

