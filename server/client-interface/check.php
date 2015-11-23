<?php
	include_once("../mods/weakcrypt.php");
	test_string="Butts\ff\00!"
	assert(Weakcrypt::decrypt(Weakcrypt::encrypt(test_tring))===test_string)
	echo(Weakcrypt::encrypt(test_string));
	?>