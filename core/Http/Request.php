<?php

namespace Core\Http;

/**
 * Request class - Wraps all HTTP request data
 * 
 * This class provides a clean interface to access request data instead of
 * using raw superglobals ($_GET, $_POST, $_SERVER, etc.) throughout the app.
 * 
 * Benefits:
 * - Cleaner, more testable code
 * - Single source of truth for request data
 * - Foundation for middleware and CSRF protection
 * - Easy to mock in tests
 */

class Request
{
    protected $query; // GET parameters
    protected $request; // POST parameters
    protected $server; // SERVER variables
    protected $files; // Uploaded files
    protected $cookies; // Cookies
    protected $headers; // HTTP headers

    public function __construct(
        array $query = [],
        array $request = [],
        array $server = [],
        array $files = [],
        // array $cookies = []

    ) {

        $this->query = $query ?: $_GET;
        $this->request = $request ?: $_POST;
        $this->server = $server ?: $_SERVER;
        $this->files = $files ?: $_FILES;
        // $this->cookies = $cookies ?: $_COOKIES;

        // Parse headers from SERVER variables
        $this->headers = $this->parseHeaders();
    }

    /**
     * Create a Request instance from PHP superglobals
     */
    public static function capture()
    {
        // return new static($_GET, $_POST, $_SERVER, $_FILES, $_COOKIES);
        return new static($_GET, $_POST, $_SERVER, $_FILES);
    }

    /**
     * Get an input value from POST or GET (POST takes priority)
     */
    public function input($key, $default = null)
    {
        // Check POST first, then GET
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * Get all input data (combines POST and GET)
     */

    public function all()
    {
        return array_merge($this->query, $this->request);
    }

    /**
     * Get only specific keys from input
     * 
     * @param array $keys Keys to retrieve
     * @return array
     */
    public function only(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[$key] = $this->input($key);
            }
        }

        return $result;
    }

    /**
     * Get all input except specific keys
     * 
     * @param array $keys to exclude
     * @return array
     */
    public function except(array $keys)
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    /**
     * Check if input key exists
     */
    public function has($key)
    {
        return isset($this->request[$key]) || isset($this->query[$key]);
    }

    /**
     * Get a query parameter (from GET)
     */
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    /**
     * Get a POST parameter
     */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $this->request;
        }
        return $this->request[$key] ?? $default;
    }

    /**
     * Get the HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    public function method()
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the request URI
     */
    public function uri()
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * Get the request path (URI without query string)
     */
    public function path()
    {
        return parse_url($this->uri(), PHP_URL_PATH) ?? '/';
    }

    /**
     * Check if this is an AJAX request
     */
    public function isAjax()
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH'])
            && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get the client IP address
     */
    public function ip()
    {

        // Check for proxied requests
        if (!empty(!$this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        } elseif (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        }
        return $this->server['REMOTE_ADDR'] ?? null;
    }

    /**
     * Get uploaded file
     */
    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if file was uploaded
     */
    public function hasFile($key)
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * GET A HEADER VALUE
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Get a cookie value
     */
    public function cookie($key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Parse HTTP headers from SERVER variables
     * 
     * Headers in $_SERVER are prefixed with HTTP_ and use underscores
     * Example: HTPP_CONTENT_TYPE -> content-type
     */
    protected function parseHeaders()
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$header] = $value;
            }
        }

        /**
         * Add content-type and content-length if present
         */
        if (isset($this->server['CONTENT_TYPE'])) {
            $headers['content-type'] = $this->server['CONTENT_TYPE'];
        }

        if (isset($this->server['CONTENT_LENGTH'])) {
            $headers['content-length'] = $this->server['CONTENT_LENGTH'];
        }

        return $headers;
    }

    /**
     * Get the full URL
     */
    public function url()
    {
        $scheme = isset($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $uri = $this->uri();
        return $scheme . '://' . $host . $uri;
    }

    /**
     * Get server variable
     */
    public function server($key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }

        return $this->server[$key] ?? $default;
    }
}
