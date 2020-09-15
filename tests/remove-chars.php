<?php

function clean($string) {
   //$string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[#;]/', '', $string); // Removes special chars.
}
$arr = [
	'https://doi.org/10.1016/j.indcrop.2018.10.053',
	'Nanopartículas de sílica liberadoras de óxido nítrico: multiplicas aplicações terapêuticas',
	'Discrimination of VOCs of the Plectranthus grandis by hydrodistillation, HS-SPME and cytotoxic activity.',
	'#achou;aaaaaa',
];
for ($i = 0; $i < sizeof($arr); $i++) {
	$out = clean($arr[$i]);
	$out .= "\n";
	echo $out;
}
