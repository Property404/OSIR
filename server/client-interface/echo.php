<?php
	/* Echo given argument from _GET["arg"] */
	echo(array_key_exists("arg",$_GET)?$_GET["arg"]:"No argument");
?>