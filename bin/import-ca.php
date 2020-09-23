#!/usr/bin/env php
<?php declare(strict_types=1);
if ( PHP_SAPI !== 'cli' ) {
	header( 'Content-Type: text/plain', true, 403 );
	die( "403 Forbidden\r\n\r\nThis script is intended to be run from the commandline only\r\n");
}
if ( sizeof( $argv ) !== 1 ) {
	// TODO make validity configurable
	echo "cat key.pem cert.pem ca.pem | " . $argv[0] . "\n";
	die( 2 );
}
require implode(DIRECTORY_SEPARATOR, [dirname(__DIR__, 1), 'src', '_autoload.php']);

use fyrkat\openssl\X509;
use fyrkat\openssl\OpenSSLException;
use fyrkat\openssl\PrivateKey;

$app = new letswifi\LetsWifiApp();
$app->registerExceptionHandler();
$realmManager = $app->getRealmManager();

$stdin = file_get_contents( 'php://stdin' );
preg_match_all( '/(^|\n)-----BEGIN( EC)? PRIVATE KEY-----\n.*?\n-----END\1 PRIVATE KEY-----($|\n)/sm', $stdin, $keys );
preg_match_all( '/(^|\n)-----BEGIN CERTIFICATE-----\n.*?\n-----END CERTIFICATE-----($|\n)/sm', $stdin, $certificates );

$keys = array_map( function( string $key ) {
		return new PrivateKey( $key );
	}, $keys[0] );
$certificates = array_map( function( string $certificate ) {
		return new X509( $certificate );
	}, $certificates[0] );

for( $i = count( $certificates ); $i--; $i >= 0 ) {
	$x509 = $certificates[$i];
	$sub = (string) $x509->getSubject();
	if ( null !== $realmManager->getCA( $sub ) ) {
		echo "Skipping $sub (already imported)\n";
		continue;
	}
	$key = null;
	foreach( $keys as $candidateKey ) {
		if ( $x509->checkPrivateKey( $candidateKey ) ) {
			$key = $candidateKey;
			break;
		}
	}
	echo 'Importing';
	if ( $key !== null ) {
		echo ' with key';
	}
	echo ":\n";
	echo 'i: ' . $x509->getIssuerSubject() . "\n";
	echo 's: ' . $sub . "\n";
	$realmManager->importCA( $x509, $key );
}
