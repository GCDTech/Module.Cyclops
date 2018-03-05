<?php

namespace Gcd\Cyclops\Http;

class CurlHttpClient
{
    /**
     * Executes an HTTP transaction and returns the response.
     *
     * @param HttpRequest $request
     * @return HttpResponse
     */
    public function getResponse(HttpRequest $request)
    {
        $uri = $request->getUrl();

        $headers = $request->getHeaders();
        $flatHeaders = [];

        foreach ($headers as $key => $value) {
            $flatHeaders[] = $key . ': ' . $value;
        }

        $flatHeaders[] = 'Connection: Keep-Alive';
        $flatHeaders[] = 'Expect:';
        $flatHeaders[] = 'Accept-Language: en-GB';
        $flatHeaders[] = 'Cache-Control: no-cache';
        $flatHeaders[] = 'User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)';

        $curl = curl_init($uri);

        curl_setopt($curl, CURLOPT_HEADER, false);

        $payload = $request->getPayload();

        switch ($request->getMethod()) {
            case 'delete':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'post':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $flatHeaders);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $httpResponse = new HttpResponse();
        $httpResponse->setResponseBody($response);
        $httpResponse->setResponseCode($responseCode);

        return $httpResponse;
    }
}