<?php

	include('config.php');
	include('class.software.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<body>

<?php
	if (!is_null($_POST['add'])) {
		$mysql = new mysqli($db_server, $db_user, $db_passwd, $db_name);
		$software = new Software($mysql);
		if ($software->add($_POST['name'], $_POST['category'], $_POST['url'], $_POST['url_regex'], $_POST['regex'])) {
			echo '<p>The software <b>'.$software->getName().'</b> has been added!</p>';
		}
		$mysql->close();
	}

?>
		<form method="post" action="">
		<p><label>Software name: </label><input type="text" name="name" /></p>
		<p><label>Category: </label><input type="text" name="category" /></p>
		<p><label>Official website url: </label><input type="text" name="url" /></p>
		<p><label>URL to check version: </label><input type="text" name="url_regex" /></p>
		<p><label>Regex to find version: </label><input type="text" name="regex" /></p>
		<input type="hidden" name="add" value="1" />
		<p><input type="submit" /></p>
		</form>
	</body>
</html>


