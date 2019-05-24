<?php

namespace taylormaguire\CWManageAPI;

use App\Contact;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use App\Customer;

class CWManageAPI
{
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
