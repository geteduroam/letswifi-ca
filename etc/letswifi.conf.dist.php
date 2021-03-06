<?php return [
	'auth.service' => 'DevAuth',
	'realm.selector' => null, // one of null, getparam or httphost
	'realm.default' => 'example.com', // used when realm.selector = null
	'realm.auth' => [
			'demo.eduroam.no' => [], // No settings needed
		],
	'pdo.dsn' => 'sqlite:' . dirname( __DIR__ ) . '/var/letswifi-dev.sqlite',
	'pdo.username' => null,
	'pdo.password' => null,
	'oauth.clients' => (require __DIR__ . DIRECTORY_SEPARATOR . 'clients.php') + [
			[
				'clientId' => 'no.fyrkat.oauth', 
				'redirectUris' => ['http://[::1]/callback/'], 
				'scopes' => ['eap-metadata', 'testscope'],
				'refresh' => false,
			],
		],
];
