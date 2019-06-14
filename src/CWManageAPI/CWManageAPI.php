<?php

namespace taylormaguire\CWManageAPI;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;

class CWManageAPI
{
    protected $guzzle;

    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function setUrl()
    {
        $url = env('CW_API_URL');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf("The URL provided[%] is not a valid format.", $url));
        }
        $this->url = rtrim($url, '/');
        return $this;
    }

    public function getUrl()
    {
        return $this->url . '/v4_6_release/apis/3.0/';
    }

    protected function processError(RequestException $exception)
    {
        echo Psr7\str($exception->getRequest());

        if ($exception->hasResponse())
        {
            echo Psr7\str($exception->getResponse());
        }
    }

    public function buildUri($resource)
    {
        $uri = $this->getUrl() . ltrim($resource, '/');

        if (strlen($url) > 2000) {
            throw new MalformedRequest(
                sprintf('The uri is too long. It is %s character(s) over the 2000 limit.', strlen($uri) - 2000)
            );
        }

        return $uri;
    }

    public function buildAuth()
    {
        return 'Basic ' . base64_encode(env('CW_API_PUBLIC_KEY') . ':' . env('CW_API_PRIVATE_KEY'));
    }

    public function getClientId()
    {
        return env('CW_CLIENT_ID');
    }

    public function getVersion()
    {
        return env('CW_API_VERSION');
    }

    public function getHeaders()
    {
        return [
            'clientId'      => $this->getClientId(), 
            'Authorization' => $this->buildAuth(),
            'Accept'        => 'application/vnd.connectwise.com+json; version=' . $this->getVersion(),
        ];
    }

    public function request($method, $resource, $headers)
    {
        try {
            $response = $this->guzzle->request(
                'GET',
                $this->buildUri($resource),
                $this->getHeaders($headers)
            );
            return $this->processResponse($response);
        } catch (RequestException $e) {
            $this->processError($e);
        }
    }

    public function processResponse(Response $response)
    {
        return json_decode($response->getBody(), true);
    }

    public function get($request, $model, $conditions, $fields, $pageSize, $pageNum, $orderBy)
    {
        $client = new Client();
        try {
            $conditions = "&conditions=" . $conditions;
            $fields = "&fields=" . $fields;
            $pageSize = "?pageSize=" . $pageSize;
            $pageNum = "&page=" . $pageNum;
            $orderBy = "&orderBy=" . $orderBy;
            $uri = env('CW_API_URL') . $model . $pageSize . $pageNum . $conditions. $fields . $orderBy;
            $result = $client->request($request, $uri, [
                'auth' => [
                    env('CW_API_PUBLIC_KEY'),
                    env('CW_API_PRIVATE_KEY')
                ]
            ]);
            $body = $result->getBody();
            $connectwise = json_decode($body, true);
        } catch (ClientException $e) {
            dd (psr7\str($e->getResponse()));
        }
        return $connectwise;
    }

    public function count($model)
    {
        $client = new Client();
        try {
            $uri = env('CW_API_URL') . $model . '/count';
            $result = $client->request('GET', $uri, [
                'auth' => [
                    env('CW_API_PUBLIC_KEY'),
                    env('CW_API_PRIVATE_KEY')
                ]
            ]);
            $body = $result->getBody();
            $connectwise = json_decode($body);
        } catch (ClientException $e) {
            dd (psr7\str($e->getResponse()));
        }
        return $connectwise;
    }

    public function get_all_companies()
    {
        $companies = [];

        $page = 1;
        $pageSize = 1000;

        $companiesCount = CWManageAPI::count('company/companies')->count;

        $pageCount = ceil(($companiesCount / $pageSize));

        while ($page <= $pageCount) {
            $res = CWManageAPI::get('GET', 'company/companies', '', 'id,name', $pageSize, $page, 'id desc');
            if (count($res) > 0) {
                $companies = array_merge($companies, $res);
            }
            $page++;
        }

        foreach ($companies as $company) {
            Customer::updateOrCreate(
                ['cw_company_id' => $company['id']],
                ['customer' => $company['name']]
            );
        }

        return $companies;
    }

    public function get_all_contacts()
    {
        $contacts = [];

        $page = 1;
        $pageSize = 1000;

        $contactsCount = CWManageAPI::count('company/contacts')->count;
        $pageCount = ceil(($contactsCount / $pageSize));

        while ($page <= $pageCount) {
            $res = CWManageAPI::get('GET', 'company/contacts', '', 'id,firstName,lastName,company/id', $pageSize, $page, 'id desc');
            if (count($res) > 0) {
                $contacts = array_merge($contacts, $res);
            }
            $page++;
        }

        $list = [];
        foreach ($contacts as $contact) {
            $data = [
                'cw_contact_id' => $contact['id'],
                'first_name' => $contact['firstName'],
                'last_name' => isset($contact['lastName']) ? $contact['lastName'] : '',
                'cw_company_id' => isset($contact['company']['id']) ? $contact['company']['id'] : null
            ];
            array_push($list, $data);
        }

        $collection = collect($list);
        $chunks = $collection->chunk(100);
        $chunks->toArray();

        foreach ($chunks as $chunk) {
            $stuff = $chunk->toArray();
            echo $stuff . "<br>";
//            Contact::insert($chunk->toArray());
//            Contact::updateOrCreate(
//                [
//                    'cw_contact_id' => $stuff['cw_contact_id']
//                ],
//                [
//                    'cw_contact_id' => $stuff['cw_contact_id'],
//                    'first_name' => $stuff['first_name'],
//                    'last_name' => $stuff['last_name'],
//                    'cw_company_id' => $stuff['cw_company_id']
//                ]
//            );
        }
        return $contacts;
    }
}
