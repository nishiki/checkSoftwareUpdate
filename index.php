<?php
	include('config.php');
	include('class.software.php');

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo '<body>'."\n";

	$mysql = new mysqli($db_server, $db_user, $db_passwd, $db_name);
	
	$software = new Software($mysql);
	echo '<table>'."\n";
	echo '<tr><th>Software</th><th>Last version</th><th>Date</th><th>Preview version</th><th>Date</th></tr>'."\n";
	foreach ($software->getIdList() as $id) {
		$soft = new Software($mysql, $id);
		echo '<tr>'."\n";
		echo '<td><a href="'.$soft->getUrl().'">'.$soft->getName().'</a></td>';
		echo '<td>'.$soft->getVersion().'</td>';
		echo '<td>'.$soft->getDate().'</td>';
		echo '<td>'.$soft->getPreviewVersion().'</td>';
		echo '<td>'.$soft->getPreviewDate().'</td>';
		echo '</tr>'."\n";
	}
	echo '</table>'."\n";
	echo '</body>'."\n";
	echo '</html>'."\n";

	$mysql->close();


