<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['secret_key']='eyJhbGciOiJIUzI1NiJ9';
$config['issuer_claim']='WEBAPP';
$config['audience_claim']='THE_AUDIENCE';
$config['jwt_algorithm'] = 'HS256'; // JWT algorithm (e.g., HS256, HS384, HS512)
