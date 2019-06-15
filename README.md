# ConnectWise Manage API for PHP/Laravel
## Warning
Currently building and testing. Not production ready.
## Installation
```php
composer require taylormaguire/connectwise-manage-api
```
## .env File Example Code
Add these details to your .env environment file with your own details as appropriate
```php
CW_API_URL=https://api-na.myconnectwise.net
CW_CLIENT_ID=7a3bedaed-73f0-441b-609c-c65e27aa3e12
CW_API_VERSION=2019.3
CW_COMPANY_ID=company
CW_API_PUBLIC_KEY=company+PflTy8uZrw9yLoz6
CW_API_PRIVATE_KEY=da34naA8Cja39aE1
```

### GET Request
Collect data from the ConnectWise Manage API using the query string parameters provided in the ConnectWise Manage API Documentation. This package will only passthrough keys that are provided by ConnectWise for Query String integrity.

Query String Parameters can be found here:
https://developer.connectwise.com/Products/Manage/Developer_Guide#Query_String_Parameters
```php
CWManageAPI::get('company/companies', [
    'pageSize' => 30,
    'page' => 1,
    'orderBy' => 'chargeToId asc',
    'fields' => 'id,company/name,status'
]);
```

The example above will output a query string of

```php
/company/companies?pageSize=30&page=1&orderBy=company/name%20%asc&fields=id,company/name,status
```
