<?php

use App\Util\Engine;

include __DIR__ . '/../vendor/autoload.php';


$data = [
	'title' => 'HELLO WORLD',
	'mainTitle' => 'HELLO WORLD',
	'users' => [
		[
			'number' => 1,
			'name' => 'Nome1'
		],
		[
			'number' => 2,
			'name' => 'Nome2'
		],
		[
			'number' => 3,
			'name' => 'Nome3'
		],
		[
			'number' => 4,
			'name' => 'Nome4'
		],
		[
			'number' => 5,
			'name' => 'Nome5'
		],
		[
			'number' => 6,
			'name' => 'Nome6'
		],
		[
			'number' => 7,
			'name' => 'Nome7'
		],
		[
			'number' => 8,
			'name' => 'Nome8'
		],
		[
			'number' => 9,
			'name' => 'Nome9'
		],
	]

];

echo Engine::render('test', $data);