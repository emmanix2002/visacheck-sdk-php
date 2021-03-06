<?php

namespace Visacheck\Visacheck;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Visacheck\Visacheck\Exception\VisacheckException;

trait SendsHttpRequestTrait
{
    /** @var array  */
    protected $headers = [];

    /** @var array  */
    protected $body = [];

    /** @var array  */
    protected $multipart = [];

    /**
     * @inheritdoc
     */
    public function requiresAuthorization(): bool
    {
        return false;
    }

    /**
     * The value for the Authorization header.
     *
     * @return string
     */
    public function getAuthorizationHeader(): string
    {
        return '';
    }

    /**
     * Does the request carry JSON data?
     *
     * @return bool
     */
    public function isJsonRequest(): bool
    {
        return true;
    }

    /**
     * The request URL.
     *
     * @return Uri
     */
    public function getRequestUrl(): Uri
    {
        return new Uri();
    }

    /**
     * Pre-fills the request header with some default values as required.
     *
     * @return $this
     */
    protected function prefillHeader()
    {
        if ($this->requiresAuthorization()) {
            $this->headers['Authorization'] = $this->getAuthorizationHeader();
        }
        return $this;
    }

    /**
     * Pre-fills the request body with whatever data is required.
     * This method should be overridden to customise what should be placed into the body by default.
     * This applies to DELETE, POST, and PUT requests, allows you to set the payload
     *
     * @return $this
     */
    protected function prefillBody()
    {
        return $this;
    }
    
    /**
     * @param array  $container
     * @param mixed $name
     * @param        $value
     * @param bool   $overwrite
     *
     * @return $this
     */
    private function addEntryToPayload(array &$container, $name, $value, bool $overwrite = false)
    {
        $keyExists = array_key_exists($name, $container);
        # check if the key already exists
        if ($keyExists && !$overwrite) {
            return $this;
        }
        if (is_null($value)) {
            if ($keyExists) {
                unset($container[$name]);
            }
            return $this;
        }
        if (!is_array($value) && !is_scalar($value)) {
            throw new \InvalidArgumentException(
                'The value for a parameter should either be a scalar type (int, string, float), or an array.'
            );
        }
        $container[$name] = $value;
        return $this;
    }
    
    /**
     * Adds an entry to the request header.
     *
     * @param string $name
     * @param mixed $value
     * @param bool   $overwrite
     *
     * @return SendsHttpRequestTrait
     */
    public function addHeader(string $name, $value, bool $overwrite = false)
    {
        return $this->addEntryToPayload($this->headers, $name, $value, $overwrite);
    }

    /**
     * Adds a parameter to the body of the request.
     *
     * @param string $name
     * @param        $value
     * @param bool   $overwrite
     *
     * @return $this
     */
    public function addBodyParam(string $name, $value, bool $overwrite = false)
    {
        return $this->addEntryToPayload($this->body, $name, $value, $overwrite);
    }

    /**
     * Adds some multipart data to the request body.
     *
     * @param string            $name
     * @param string|resource   $content the string content for the key; or resource gotten from fopen()
     * @param string|null       $filename
     * @param bool              $overwrite
     *
     * @return $this
     */
    public function addMultipartParam(string $name, $content, string $filename = null, bool $overwrite = false)
    {
        if (array_key_exists($name, $this->multipart) && !$overwrite) {
            return $this;
        }
        if (!is_array($content)) {
            # not array content
            $part = ['name' => $name, 'contents' => $content];
            if (!empty($filename)) {
                $part['filename'] = $filename;
            }
            $this->multipart[] = $part;
        } else {
            # we have some array data
            $index = 0;
            foreach ($content as $c) {
                $part = ['name' => $name . '[' . $index . ']', 'contents' => $c];
                if (!empty($filename)) {
                    $part['filename'] = $filename;
                }
                $this->multipart[] = $part;
                ++$index;
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        throw new VisacheckException('You should override this method.');
    }

    /**
     * Sends a HTTP request.
     *
     * @param string $method
     * @param Client $httpClient
     * @param array  $path additional components for the path; e.g.: [$id, 'prices']
     *
     * @return VisacheckResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $method, Client $httpClient, array $path = []): VisacheckResponse
    {
        $this->prefillHeader();
        $this->prefillBody();
        if (strtolower($method) !== 'get') {
            # we don't validate GEt requests
            $this->validate();
        }
        $uri = static::getRequestUrl($path);
        $url = $uri->getScheme() . '://' . $uri->getAuthority() . $uri->getPath();
        # set the URL
        try {
            $options = [];
            # the request data
            if (!empty($this->headers)) {
                $options[RequestOptions::HEADERS] = $this->headers;
            }
            if (!empty($uri->getQuery())) {
                # some query parameters are present in the URL
                $options[RequestOptions::QUERY] = parse_query_parameters($uri->getQuery());
            }
            if (strtolower($method) !== 'get') {
                # not a get request
                if (!empty($this->multipart)) {
                    # check if we have some multipart data first
                    foreach ($this->body as $key => $value) {
                        # add the requested body params to the multipart data
                        if (!is_array($value)) {
                            $this->multipart[] = ['name' => $key, 'contents' => $value];
                        } else {
                            foreach ($value as $innerKey => $innerValue) {
                                $this->multipart[] = ['name' => $key . '[' . $innerKey . ']', 'contents' => $innerValue];
                            }
                        }
                    }
                    $options[RequestOptions::MULTIPART] = $this->multipart;

                } elseif (static::isJsonRequest() && !empty($this->body)) {
                    # a JSON request
                    $options[RequestOptions::JSON] = $this->body;

                } elseif (!empty($this->body)) {
                    # we switch to an application/www-form-urlencoded type
                    $options[RequestOptions::FORM_PARAMS] = $this->body;
                }
            }
            $response = $httpClient->request($method, $url, $options);
            return new VisacheckResponse((string) $response->getBody());

        } catch (BadResponseException $e) {
            // in the case of a failure, let's know the status
            return new VisacheckResponse((string) $e->getResponse()->getBody(), $e->getResponse()->getStatusCode(), $e->getRequest());

        } catch (ConnectException $e) {
            return new VisacheckResponse('{"status": "error", "data": "'.$e->getMessage().'"}', 0);
        }
    }
}