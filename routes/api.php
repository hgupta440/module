<?php
/*
	File used to manage all Routes exist in system.
	Routes have to declear in thier respective array
	Routes required Authuntication have to mention under AUTH array. 

*/
return [
			"GET" => [

				"module" => "app\http\Module@list",
			],
			"POST" => [
				"login" => "app\http\Login@login",
				"register" => "app\http\Login@register",
				"module" => "app\http\Module@add",
				"file" => "app\http\File@add",
			],
			"AUTH" =>[
				"GET" => [
						"module",
				],
				"POST" => [
						"module","file",
				]
			]

	];