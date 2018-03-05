<?php

namespace Gcd\Cyclops\Http;

class HttpResponse
{
    private $headers = [];

    private $responseBody = "";
    private $responseCode = "";

    public function getHeader($header, $defaultValue = null)
    {
        return (isset($this->headers[$header])) ? $this->headers[$header] : $defaultValue;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $responseBody
     */
    public function setResponseBody($responseBody)
    {
        $this->responseBody = $responseBody;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    public function isSuccess()
    {
        return $this->responseCode >= 200 && $this->responseCode <= 299;
    }

    public function isRedirect()
    {
        return $this->responseCode >= 300 && $this->responseCode <= 399;
    }

    public function isRequestError()
    {
        return $this->responseCode >= 400 && $this->responseCode <= 499;
    }

    public function isServerError()
    {
        return $this->responseCode >= 500 && $this->responseCode <= 599;
    }
}