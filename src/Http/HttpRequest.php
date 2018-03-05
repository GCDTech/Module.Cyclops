<?php

namespace Gcd\Cyclops\Http;

class HttpRequest
{
    private $url;

    private $method;

    private $payload = null;

    private $headers = [];

    public function __construct($url, $method = "get", $payload = null)
    {
        $this->method = $method;
        $this->payload = $payload;
        $this->url = $url;
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param mixed $uri
     */
    public function setUrl($uri)
    {
        $this->url = $uri;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }
}