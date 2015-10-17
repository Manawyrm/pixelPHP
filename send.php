#!/usr/bin/php
<?php
if ($argc != 4)
{
	die("Error: php send.php [IP] [port] [image]");
}
$fp = fsockopen( $argv[1] , $argv[2] , $errno, $errstr, 30);
if (!$fp)
{
    echo "$errstr ($errno)<br />\n";
    die();
}
else 
{
	// try to guess canvas size
	fwrite($fp, "SIZE\n");
	$size = "";
	while (strpos($size, "\n") === false)
	{
        $size .= fgets($fp, 128);
    }
    $size = trim($size);
    $size = explode(" ", $size);

	$output = "";
	fwrite($fp, "OFFSET ".rand ( 0, $size[1] )." ".rand ( 0, $size[2] )."\n");

    $im = imagecreatefromjpeg ( $argv[3] );

 	/*if ( ($size[1] < imagesx($im)) || ($size[2] < imagesy($im)) )
    {
    	echo "Image too large for canvas. Resizing. X:".$size[1]." Y:".$size[2]."\n"; 

    	$im = imagescale ( $im , imagesx($im) / 2, imagesy($im) / 2 );

    }*/

    echo "Converting image too string-data\n";
	for ($x=0; $x < imagesx($im); $x++)
	{ 
		for ($y=0; $y < imagesy($im); $y++)
		{ 
			$rgb = imagecolorat($im, $x, $y);
			$output .= "PX $x $y ".substr("000000".dechex($rgb),-6)."\n";
		}
	}
	echo "sending image\n";
	fwrite($fp, $output);
    fclose($fp);
}
