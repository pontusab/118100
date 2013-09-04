<?php
require 'vendor/autoload.php';

use HundraArtonHundra\HundraArtonHundra as HundraArtonHundra;

HundraArtonHundra::$apiKey = '';

$HundraArtonHundra = new HundraArtonHundra;

$response = $HundraArtonHundra->search('Phone number or name');

foreach( $response as $response ) 
{
	print_r($response);
}
