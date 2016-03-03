<?php

// Proxy HTTP Requests to get around CORS limitations
// Simply make the request to this page, but add a 'X-Proxy-Url' header with the real URL you want to make the request to.
// https://github.com/handstandsam/php-guzzle-http-proxy

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

// Helper function
function __($key, array $array, $default = null)
{
	return array_key_exists($key, $array) ? $array[$key] : $default;
}

// Instantiate an instance of the GuzzleHttp Client
$client = new Client();

//--------------------
if( ! isset($curl_timeout))
	$curl_timeout = 30;

// Get request attributes
$headers = getallheaders();
$method = __('REQUEST_METHOD', $_SERVER);
$url = __('X-Proxy-Url', $headers);

// Check that we have a URL
if( ! $url)
	http_response_code(400) and exit("X-Proxy-Url header missing");

// Check that the URL looks like an absolute URL
if( ! parse_url($url, PHP_URL_SCHEME))
	http_response_code(403) and exit("Not an absolute URL: $url");


// Remove ignored headers and prepare the rest for resending
$ignore = ['Cookie', 'Host', 'X-Proxy-URL'];
$headers = array_diff_key($headers, array_flip($ignore));
$body = "";

// Method specific options
switch($method)
{
	case 'GET':
		break;
	case 'PUT':
	case 'POST':
	case 'DELETE':
	default:
		// Capture the post body of the request to send along
		$body = file_get_contents('php://input');
		break;
}

try {
	//Create an HTTP request
	$request = $client->createRequest($method, $url, ['headers' => $headers,'body' => $body, 'decode_content' => false]);
	//Make the HTTP request
	$response = $client->send($request);
} catch (RequestException $e) {
    if ($e->hasResponse()) {
        $response=$e->getResponse();
    }
} catch (ServerException $e) {
	echo $e;
	echo "=================";
    if ($e->hasResponse()) {
        $response=$e->getResponse();
    }
}


// Remove any existing headers
header_remove();

//Print all the headers except for "Transfer-Encoding" because chunked responses will end up failing.
foreach ($response->getHeaders() as $key => $value) {
	if($key!="Transfer-Encoding"){
		header("$key: $value[0]");
	}
}

//Print the response code
http_response_code($response->getStatusCode());

// And finally the body
echo $response->getBody();