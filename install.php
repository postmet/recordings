<?php

global $amp_conf;
global $db;

require_once($amp_conf['AMPWEBROOT'] . '/admin/modules/recordings/functions.inc.php');

$fcc = new featurecode('recordings', 'record_save');
$fcc->setDescription('Save Recording');
$fcc->setDefault('*77');
$fcc->update();
unset($fcc);

$fcc = new featurecode('recordings', 'record_check');
$fcc->setDescription('Check Recording');
$fcc->setDefault('*99');
$fcc->update();
unset($fcc);

// Make sure table exists
$sql = "CREATE TABLE IF NOT EXISTS recordings ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, displayname VARCHAR(50) , filename VARCHAR(80), description VARCHAR(254));";
$result = $db->query($sql);
if(DB::IsError($result)) {
        die($result->getDebugInfo());
}

// load up any recordings that might be in the directory
$recordings_directory = "/var/lib/asterisk/sounds/custom/";
if (!is_writable($recordings_directory)) {
	print "<h2>Error</h2><br />I can not access the directory $recordings_directory. ";
	print "Please make sure that it exists, and is writable by the web server.";
	die;
}
$dh = opendir($recordings_directory);
while (false !== ($file = readdir($dh))) { // http://au3.php.net/readdir 
	if ($file[0] != "." && $file != "CVS" && $file != "svn") {
		// Ignore the suffix..
		$fname = ereg_replace('.wav', '', $file);
		if (recordings_get_id("custom/$fname") == null)
			recordings_add($fname, "custom/$file");
	}
}
$result = sql("INSERT INTO recordings values ('', '__invalid', 'install done', '')");

?>