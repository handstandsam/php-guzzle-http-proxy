Proxy for HTTP Requests including GET, POST, PUT and DELETE

Usage: Make a normal request to this php page.  The only difference is you'll add an 'X-Proxy-Url' header where the URL you want to hit will go.  That's it.

Example (assuming you have this code in a directory named "proxy", running on localhost)
* curl -v 'http://localhost/proxy/' -H 'X-Proxy-Url: https://www.google.com' --compressed

Requires: 
* PHP 5.4.0 or later.

Uses Library (included in repository):
* Guzzle 5.3 (HTTP Client) - http://docs.guzzlephp.org/en/5.3/index.html

Attributions:
* Code is a derivative of https://github.com/Svish/php-cross-domain-proxy, but added support for responses that contained the 'Transfer-Encoding: chunked' header.  This is done by leveraging the Guzzle client.
