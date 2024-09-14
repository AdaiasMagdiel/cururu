# Cururu

**Cururu** is a lightweight PHP library designed to simplify making HTTP requests with cURL. It offers a minimalistic approach to handling web APIs, providing an easy-to-use interface with essential features. This makes it ideal for smaller projects that need efficient HTTP request handling without the overhead of larger libraries.

## Features

- Simple and intuitive interface for making HTTP requests.
- Optional base URL configuration, or you can use full URLs directly in requests.
- Configurable headers and timeouts.
- SSL verification toggle for secure or insecure requests.
- cURL error handling and automatic response parsing.
- Support for JSON, form-data, and URL-encoded bodies in POST requests.
- Lightweight and easy to integrate into any PHP project.

## Installation

To include Cururu in your project, use [Composer](https://getcomposer.org/). First, ensure you have Composer installed, then run the following command:

```bash
composer require adaiasmagdiel/cururu
```

## Usage

### Basic Example

```php
<?php

require 'vendor/autoload.php';

use AdaiasMagdiel\Cururu\Cururu;

// Initialize Cururu with optional base URL and headers
$cururu = new Cururu('https://api.example.com', ['Authorization' => 'Bearer your_token']);

// Make a GET request
$response = $cururu->get('/resource');

// Get response content
if ($response->isSuccessful()) {
    echo $response->getContent();
} else {
    echo "Request failed with status: " . $response->getStatusCode();
}

// Make a POST request with JSON data
$response = $cururu->post('/resource', ['name' => 'Cururu'], ['Content-Type' => 'application/json']);

if ($response->isSuccessful()) {
    $json = $response->getJson();
    print_r($json);
}
```

### Using Full URLs without Base URL

Cururu allows you to skip the base URL entirely and use full URLs directly in the request methods.

```php
$cururu = new Cururu();

// Make a GET request using the full URL
$response = $cururu->get('https://api.example.com/resource');
```

### Setting Custom Headers

You can set additional headers for each request or globally for the instance.

```php
$cururu = new Cururu('https://api.example.com', ['Authorization' => 'Bearer your_token']);
$customHeaders = ['Custom-Header' => 'CustomValue'];

$response = $cururu->get('/resource', $customHeaders);
```

### Handling POST Requests

Cururu supports different content types for POST requests, such as `application/json` and `application/x-www-form-urlencoded`.

```php
$data = ['name' => 'Cururu', 'description' => 'Simple HTTP client'];
$response = $cururu->post('/resource', $data, ['Content-Type' => 'application/json']);

if ($response->isSuccessful()) {
    echo "Resource created successfully!";
}
```

### Error Handling

Cururu provides error handling for cURL operations. In case of a request failure, an exception will be thrown with a descriptive error message.

```php
try {
    $response = $cururu->get('/invalid-endpoint');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Response Handling

Cururu provides a `Response` object that encapsulates the HTTP response, offering methods to work with the response content, status code, and headers.

```php
$response = $cururu->get('/resource');

// Get status code
$status = $response->getStatusCode();

// Get response content as JSON
$data = $response->getJson();

// Get specific header
$contentType = $response->getHeader('Content-Type');
```

## License

Cururu is licensed under the [MIT License](LICENSE).

## Contributing

Contributions are welcome! Please fork this repository and submit a pull request with your changes.
