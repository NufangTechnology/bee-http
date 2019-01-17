<?php
namespace Bee\Http;

use Bee\Http\Response\Headers;
use Bee\Http\Response\Exception;
use Bee\Http\Response\HeadersInterface;

/**
 * Response
 *
 * @package Ant\Http
 */
class Response implements ResponseInterface
{
    /**
     * @var \Swoole\Http\Response
     */
    protected $response;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var bool
     */
    protected $sent = false;

    /**
     * 数据源
     *
     * @param \Swoole\Http\Response $response
     */
    public function withSource(\Swoole\Http\Response $response)
    {
        $this->response = $response;
        $this->headers  = new Headers();

        // 更新发送状态
        $this->sent     = false;
    }

    /**
     * Sets the HTTP response code
     *
     * <code>
     *     $response->setStatusCode(404, "Not Found");
     * </code>
     *
     * @param int $code
     * @param string|null $message
     * @return Response
     * @throws Exception
     */
    public function setStatusCode(int $code, string $message = null): ResponseInterface
    {
        if ($message == null) {
            $statusCodes = [
                // INFORMATIONAL CODES
                100 => "Continue",                        // RFC 7231, 6.2.1
				101 => "Switching Protocols",             // RFC 7231, 6.2.2
				102 => "Processing",                      // RFC 2518, 10.1
				103 => "Early Hints",
				// SUCCESS CODES
				200 => "OK",                              // RFC 7231, 6.3.1
				201 => "Created",                         // RFC 7231, 6.3.2
				202 => "Accepted",                        // RFC 7231, 6.3.3
				203 => "Non-Authoritative Information",   // RFC 7231, 6.3.4
				204 => "No Content",                      // RFC 7231, 6.3.5
				205 => "Reset Content",                   // RFC 7231, 6.3.6
				206 => "Partial Content",                 // RFC 7233, 4.1
				207 => "Multi-status",                    // RFC 4918, 11.1
				208 => "Already Reported",                // RFC 5842, 7.1
				226 => "IM Used",                         // RFC 3229, 10.4.1
				// REDIRECTION CODES
				300 => "Multiple Choices",                // RFC 7231, 6.4.1
				301 => "Moved Permanently",               // RFC 7231, 6.4.2
				302 => "Found",                           // RFC 7231, 6.4.3
				303 => "See Other",                       // RFC 7231, 6.4.4
				304 => "Not Modified",                    // RFC 7232, 4.1
				305 => "Use Proxy",                       // RFC 7231, 6.4.5
				306 => "Switch Proxy",                    // RFC 7231, 6.4.6 (Deprecated)
				307 => "Temporary Redirect",              // RFC 7231, 6.4.7
				308 => "Permanent Redirect",              // RFC 7538, 3
				// CLIENT ERROR
				400 => "Bad Request",                     // RFC 7231, 6.5.1
				401 => "Unauthorized",                    // RFC 7235, 3.1
				402 => "Payment Required",                // RFC 7231, 6.5.2
				403 => "Forbidden",                       // RFC 7231, 6.5.3
				404 => "Not Found",                       // RFC 7231, 6.5.4
				405 => "Method Not Allowed",              // RFC 7231, 6.5.5
				406 => "Not Acceptable",                  // RFC 7231, 6.5.6
				407 => "Proxy Authentication Required",   // RFC 7235, 3.2
				408 => "Request Time-out",                // RFC 7231, 6.5.7
				409 => "Conflict",                        // RFC 7231, 6.5.8
				410 => "Gone",                            // RFC 7231, 6.5.9
				411 => "Length Required",                 // RFC 7231, 6.5.10
				412 => "Precondition Failed",             // RFC 7232, 4.2
				413 => "Request Entity Too Large",        // RFC 7231, 6.5.11
				414 => "Request-URI Too Large",           // RFC 7231, 6.5.12
				415 => "Unsupported Media Type",          // RFC 7231, 6.5.13
				416 => "Requested range not satisfiable", // RFC 7233, 4.4
				417 => "Expectation Failed",              // RFC 7231, 6.5.14
				418 => "I'm a teapot",                    // RFC 7168, 2.3.3
				421 => "Misdirected Request",
				422 => "Unprocessable Entity",            // RFC 4918, 11.2
				423 => "Locked",                          // RFC 4918, 11.3
				424 => "Failed Dependency",               // RFC 4918, 11.4
				425 => "Unordered Collection",
				426 => "Upgrade Required",                // RFC 7231, 6.5.15
				428 => "Precondition Required",           // RFC 6585, 3
				429 => "Too Many Requests",               // RFC 6585, 4
				431 => "Request Header Fields Too Large", // RFC 6585, 5
				451 => "Unavailable For Legal Reasons",   // RFC 7725, 3
				499 => "Client Closed Request",
				// SERVER ERROR
				500 => "Internal Server Error",           // RFC 7231, 6.6.1
				501 => "Not Implemented",                 // RFC 7231, 6.6.2
				502 => "Bad Gateway",                     // RFC 7231, 6.6.3
				503 => "Service Unavailable",             // RFC 7231, 6.6.4
				504 => "Gateway Time-out",                // RFC 7231, 6.6.5
				505 => "HTTP Version not supported",      // RFC 7231, 6.6.6
				506 => "Variant Also Negotiates",         // RFC 2295, 8.1
				507 => "Insufficient Storage",            // RFC 4918, 11.5
				508 => "Loop Detected",                   // RFC 5842, 7.2
				510 => "Not Extended",                    // RFC 2774, 7
				511 => "Network Authentication Required"  // RFC 6585, 6
            ];

            if (!isset($statusCodes[$code])) {
                throw new Exception('Non-standard statusCode given without a message');
            }

            $message = $statusCodes[$code];
        }

        $value = $code . ' ' . $message;

        $this->headers->set('HTTP/1.1', $value);
        $this->headers->set('Status', $value);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatusCode()
    {
        $statusCode = substr($this->getHeaders()->get('Status'), 0, 3);
        
        return $statusCode ? (int)$statusCode : null;
    }

    /**
     * Returns headers set by the user
     */
    public function getHeaders(): HeadersInterface
    {
        return $this->headers;
    }

    /**
     * Overwrites a header in the response
     *
     * @param string $name
     * @param $value
     * @return Response
     */
    public function setHeader(string $name, $value): ResponseInterface
    {
        $this->headers->set($name, $value);
        
        return $this;
    }

    /**
     * Send a raw header to the response
     *
     * @param string $header
     * @return Response
     */
    public function setRawHeader(string $header): ResponseInterface
    {
        $this->headers->setRaw($header);
        
        return $this;
    }

    /**
     * Resets all the established headers
     */
    public function resetHeaders(): ResponseInterface
    {
        $this->headers->reset();
        
        return $this;
    }

    /**
     * Sets output expire time header
     *
     * @param \DateTime $datetime
     * @return Response
     */
    public function setExpires(\DateTime $datetime): ResponseInterface
    {
        $date = clone $datetime;
        /**
         * All the expiration times are sent in UTC
         * Change the timezone to utc
         */
        $date->setTimezone(new \DateTimeZone('UTC'));

        /**
         * The 'Expires' header set this info
         */
        $this->setHeader('Expires', $date->format('D, d M Y H:i:s') . ' GMT');
        
        return $this;
    }
    
    public function setLastModified(\DateTime $datetime) : ResponseInterface
    {
        $date = clone $datetime;

        /**
         * All the expiration times are sent in UTC
         * Change the timezone to utc
         */
        $date->setTimezone(new \DateTimeZone('UTC'));

        /**
         * The 'Last-Modified' header sets this info
         */
        $this->setHeader("Last-Modified", $date->format("D, d M Y H:i:s") . " GMT");

        return $this;
    }

    /**
     * @param int $minutes
     * @return Response
     * @throws \Exception
     */
    public function setCache(int $minutes) : ResponseInterface
    {
        $date = new \DateTime();
        $date->modify('+' . $minutes . ' minutes');

        $this->setExpires($date);
        $this->setHeader('Cache-Control', 'max-age=' . ($minutes * 60));

        return $this;
    }

    /**
     * Sends a Not-Modified response
     *
     * @throws Exception
     */
    public function setNotModified(): ResponseInterface
    {
        $this->setStatusCode(304, "Not modified");

        return $this;
    }

    /**
     * Sets the response content-type mime, optionally the charset
     *
     * @param string $contentType
     * @param string $charset
     * @return Response
     */
    public function setContentType(string $contentType, $charset = null): ResponseInterface
    {
        if ($charset === null) {
            $this->setHeader('Content-Type', $contentType);
        } else {
            $this->setHeader('Content-Type', $contentType . '; charset=' . $charset);
        }

        return $this;
    }

    /**
     * Sets the response content-length
     *
     * @param int $contentLength
     * @return Response
     */
    public function setContentLength(int $contentLength): ResponseInterface
    {
        $this->setHeader("Content-Length", $contentLength);

		return $this;
    }

    /**
     * Set a custom ETag
     *
     *<code>
     * $response->setEtag(md5(time()));
     *</code>
     *
     * @param string $etag
     * @return Response
     */
    public function setEtag(string $etag) : ResponseInterface
	{
		$this->setHeader("Etag", $etag);

		return $this;
	}

    /**
     * Redirect by HTTP to another URL
     *
     *<code>
     * // Using a string redirect (internal/external)
     * $response->redirect("http://en.wikipedia.org", true);
     * $response->redirect("http://www.example.com/new-location", true, 301);
     *
     * @param null $location
     * @param bool $externalRedirect
     * @param int $statusCode
     * @return Response
     * @throws Exception
     */
    public function redirect($location = null, bool $externalRedirect = false, int $statusCode = 302): ResponseInterface
    {
        $header = '';

        if (!$location) {
            $location = '';
        }

        if ($externalRedirect) {
            $header = $location;
        }

        /**
         * The HTTP status is 302 by default, a temporary redirection
         */
        if ($statusCode < 300 || $statusCode > 308) {
            $statusCode = 302;
        }

        /**
         * Change the current location using 'Location'
         */
        $this->setStatusCode($statusCode);

        $this->setHeader('Location', $header);

        return $this;
    }

    /**
     * Sets HTTP response body
     *
     * @param string $content
     * @return Response
     */
    public function setContent(string $content): ResponseInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Sets HTTP response body. The parameter is automatically converted to JSON
     *
     *<code>
     * $response:setJsonContent(
     *     [
     *         "status" => "OK",
     *     ]
     * );
     *</code>
     *
     * @param $content
     * @param int $jsonOptions
     * @param int $depth
     * @return Response
     */
    public function setJsonContent($content, int $jsonOptions = 0, int $depth = 512): ResponseInterface
    {
        $this->setContentType('application/json', 'UTF-8');
        $this->setContent(json_encode($content, $jsonOptions, $depth));

        return $this;
    }

    /**
     * Appends a string $to the HTTP response body
     *
     * @param $content
     * @return Response
     */
    public function appendContent($content): ResponseInterface
    {
        $this->content = $this->getContent() . $content;

        return $this;
    }

    /**
     * Gets the HTTP response body
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isSent() : bool
    {
        return $this->sent;
    }

    /**
     * Sends headers to the client
     */
    public function sendHeaders(): ResponseInterface
    {
        $headers = $this->headers->toArray();

        // 发送 header
        foreach ($headers as $key => $value) {
            $this->response->header($key, $value);
        }

        return $this;
    }

    /**
     * Prints out HTTP response to the client
     *
     * @throws Exception
     */
    public function send(): ResponseInterface
    {
        if ($this->sent) {
            throw new Exception('Response was already sent');
        }

        $this->sendHeaders();

        /**
         * Output the response body
         */
        if ($this->content) {
            $this->response->write($this->content);
        } else {
            if ($this->file) {
                $this->response->sendfile($this->file);
            }
        }

        $this->sent = true;

        $this->response->end();

        return $this;
    }

    /**
     * Sets an attached file to be sent at the end of the request
     *
     * @param string $filePath
     * @param mixed $attachmentName
     * @param mixed $attachment
     * @return Response
     */
    public function setFileToSend(string $filePath, $attachmentName = null, $attachment = null) : ResponseInterface
    {
        if (is_string($attachmentName)) {
            $basePath = basename($filePath);
        } else {
            $basePath = $attachmentName;
        }

        if ($attachment) {
            $this->setRawHeader('Content-Description: File Transfer');
			$this->setRawHeader('Content-Type: application/octet-stream');
			$this->setRawHeader('Content-Disposition: attachment; filename=' . $basePath);
			$this->setRawHeader('Content-Transfer-Encoding: binary');
        }

        $this->file = $filePath;

        return $this;
    }

    /**
     * Remove a header in the response
     *
     *<code>
     * $response->removeHeader("Expires");
     *</code>
     *
     * @param string $name
     * @return Response
     */
    public function removeHeader(string $name) : ResponseInterface
    {
        $this->headers->remove($name);

        return $this;
    }
}
