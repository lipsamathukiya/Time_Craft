<?php
$f = file_get_contents(__DIR__ . '/../product_details.php');
preg_match_all('/.*stock.*/i', $f, $matches);
print_r($matches[0]);
