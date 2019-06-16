# ConnectWise Manage API for Laravel / PHP
This is a package created to simplify connection requests to the ConnectWise Manage API for Laravel and PHP based applications.

## Warning
This package is currently in development. Not suggested for production at this time.

## Requirements
- Guzzle 6.3
- Laravel 5.7+ 

## Installation

### Composer Installation
Install the package through composer in your terminal.

```
composer require taylormaguire/connectwise-manage-api
```

### Setup The Environment (.env File) for Authentication
Add these details to your .env environment file with your own details as appropriate. This package utilizes the ConnectWise Manage Member Authentication method. Details are found [here](https://developer.connectwise.com/Products/Manage/Developer_Guide#Authentication).

Each Environment Variable is REQUIRED for a successful connection.

```
CW_API_URL=
CW_CLIENT_ID=
CW_API_VERSION=
CW_COMPANY_ID=
CW_API_PUBLIC_KEY=
CW_API_PRIVATE_KEY=
```

#### API URL (CW_API_URL)
Input your Manage URL. For Cloud or Staging servers you must put "api-" in front of the ConnectWise Manage URL.

```
https://api-au.myconnectwise.net
https://api-eu.myconnectwise.net
https://api-na.myconnectwise.net
https://api-staging.myconnectwise.net
```

#### Client ID (CW_CLIENT_ID)
The Client ID is now required per application as of version 2019.3 of the Manage API. Learn about the Client ID and Generate your applications Client ID [here](https://developer.connectwise.com/ClientID#What_is_a_clientId).

#### API Version (CW_API_VERSION)
Default recommendation is 2019.3 however you can use an older version by changing this environment variable.

#### Company ID (CW_COMPANY_ID)
Every ConnectWise Manage instance has a Company ID used for login.

#### Public Key (CW_API_PUBLIC_KEY) & Private Key (CW_API_PRIVATE_KEY)
The Public Key is a combination of your Company ID and your Public Key. For Example
```
company+PflTy8uZrw9yLoz6
```
The Public & Private Keys are generated from inside of the ConnectWise Manage application. For instructions on how to create this account go [here](https://developer.connectwise.com/Products/Manage/Developer_Guide#Authentication).

#### Complete Environment Example

```
CW_API_URL=https://api-na.myconnectwise.net
CW_CLIENT_ID=7a3bedaed-73f0-441b-609c-c65e27aa3e12
CW_API_VERSION=2019.3
CW_COMPANY_ID=company
CW_API_PUBLIC_KEY=company+PflTy8uZrw9yLoz6
CW_API_PRIVATE_KEY=da34naA8Cja39aE1
```
## Usage
### GET Request
Collect data from the ConnectWise Manage API using the query string parameters provided in the ConnectWise Manage API Documentation. This package will only passthrough keys that are provided by ConnectWise for Query String integrity.

Accepted Query String Parameters can be found [here](https://developer.connectwise.com/Products/Manage/Developer_Guide#Query_String_Parameters).

#### Example GET Request
Here is an GET Request Example using the Facade

```php
use taylormaguire\CWManageAPI\CWManageAPI;

CWManageAPI::get('company/companies', [
    'pageSize' => 30,
    'page' => 1,
    'orderBy' => 'chargeToId asc',
    'fields' => 'id,company/name,status'
]);
```

The example above will output a query string of

```
https://api-na.myconnectwise.net/company/companies?pageSize=30&page=1&orderBy=company/name%20%asc&fields=id,company/name,status
```

#### Query String Parameters
![Screenshot](https://github.com/taylormaguire/connectwise-manage-api/blob/master/connectwise_query_string_parameters.png)
#### Conditionals
![Screenshot](https://github.com/taylormaguire/connectwise-manage-api/blob/master/connectwise_conditionals.png)
