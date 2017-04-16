#!/usr/bin/php
<?php
ini_set("memory_limit", "8192M");
$output = [];

$im = imagecreatefromjpeg ( $argv[1] );

echo "Converting image to string-data\n";
for ($x=0; $x < imagesx($im); $x++)
{ 
	for ($y=0; $y < imagesy($im); $y++)
	{ 
		$rgb = imagecolorat($im, $x, $y);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		$ip = "2001:67c:20a1:1041:" . dechex2($x) . ":" .  dechex2($y) . ":" . dechex2($r) . dechex2($g) . ":" . dechex2($b) . "00";

		$output[] = ['x' => $x, 'y' => $y, 'ip' => $ip];
	}
}
echo "sending image\n";

shuffle($output);
$message = 1;

$socket = socket_create( AF_INET6, SOCK_DGRAM, SOL_UDP );
while (true)
{
	foreach ($output as $pixel)
	{
		socket_sendto( $socket, $message, 1, 0, $pixel['ip'], 1337 );
	}
	echo("draw finished.\n");
}
socket_close ( $socket );

function dechex2 ($binary)
{
	return sprintf('%02x', $binary);
}