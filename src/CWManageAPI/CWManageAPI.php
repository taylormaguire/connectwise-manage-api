<?php

namespace taylormaguire\CWManageAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;

class CWManageAPI
{
    protected $request;

    private static $with = [
        'pageSize',
        'page',
        'fields',
        'columns',
        'orderBy',
        'customFieldConditions',
        'childConditions',
        'conditions',
    ];

    private static $with_all = [
        'fields',
        'columns',
        'orderBy',
        'customFieldConditions',
        'childConditions',
        'conditions',
    ];

    public function __construct()
    {

    }

    public function url()
    {
        $url = env('CW_API_URL');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf("The URL provided[%] is not a valid format.", $url));
        }

        return rtrim(env('CW_API_URL'), '/') . '/v4_6_release/apis/3.0/';
    }

    protected function error(RequestException $exception)
    {
        echo Psr7\str($exception->getRequest());

        if ($exception->hasResponse()) {
            echo Psr7\str($exception->getResponse());
        }
    }

    protected function response(Response $response)
    {
        return json_decode($response->getBody(), false);
    }

    public function uri($resource)
    {
        $uri = $this->url() . ltrim($resource, '/');

        if (strlen($uri) > 2000) {
            throw new MalformedRequest(
                sprintf('The uri is too long. It is %s character(s) over the 2000 limit.', strlen($uri) - 2000)
            );
        }

        return $uri;
    }

    public function auth()
    {
        return 'Basic ' . base64_encode(env('CW_API_PUBLIC_KEY') . ':' . env('CW_API_PRIVATE_KEY'));
    }

    public function clientId()
    {
        return env('CW_CLIENT_ID');
    }

    public function version()
    {
        return env('CW_API_VERSION');
    }

    public function headers()
    {
        return [
            'clientId' => $this->clientId(),
            'Authorization' => $this->auth(),
            'Accept' => 'application/vnd.connectwise.com+json; version=' . $this->version(),
        ];
    }

    public function request($method, $model)
    {
        try {
            $client = new Client();
            $response = $client->request(
                $method,
                $this->uri($model),
                [
                    'headers' => $this->headers(),
                ]
            );
            return $this->response($response);
        } catch (RequestException $e) {
            $this->error($e);
        }
    }

    public static function count($model)
    {
        $request = new CWManageAPI();
        return $request->request('GET', $model . '/count');
    }

    public static function get($model, $options = [])
    {
        $with = self::$with;
        $options = array_filter($options,
            function ($key) use ($with) {
                return in_array($key, $with);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($options)) {
            $model = $model . '?';
        }

        foreach ($options as $key => $option) {
            $model = $model . $key . '=' . $option . '&';
        }

        $request = new CWManageAPI();
        return $request->request('GET', $model);
    }

    public static function get_all($model, $options = [])
    {
        $count = CWManageAPI::count($model)->count;

        $with = self::$with_all;
        $options = array_filter($options,
            function ($key) use ($with) {
                return in_array($key, $with);
            },
            ARRAY_FILTER_USE_KEY
        );

        $page = 1;
        $pageSize = 1000;

        $model = $model . '?pageSize=' . $pageSize . '&';

        foreach ($options as $key => $option) {
            $model = $model . $key . '=' . $option . '&';
        }

        $data = [];

        $request = new CWManageAPI();

        $pageCount = ceil(($count / $pageSize));

        while ($page <= $pageCount) {
            $response = $request->request('GET', $model . '&page=' . $page);
            if (count($response) > 0) {
                $data = array_merge($data, $response);
            }
            $page++;
        }

        return $data;
    }

    public static function post($model)
    {

    }

    public static function put($model)
    {

    }

    public static function patch($model)
    {

    }

    public static function delete($model)
    {

    }

}