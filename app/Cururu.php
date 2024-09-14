<?php

namespace AdaiasMagdiel\Cururu;

use CurlHandle;
use Exception;

class Cururu
{
	private string $baseUrl = "";
	private array $headers = [];
	private int $timeout = 30;
	private bool $verifySsl;

	public function __construct(string $baseUrl = "", array $headers = [], int $timeout = 30, bool $verifySsl = true)
	{
		$this->baseUrl = rtrim($baseUrl, '/');
		$this->setHeaders($headers);
		$this->timeout = $timeout;
		$this->verifySsl = $verifySsl;
	}

	public function setHeaders(array $headers): void
	{
		foreach ($headers as $key => $value) {
			if (!is_string($key) || !is_string($value)) {
				throw new Exception("Headers must be in the format ['Key' => 'Value']");
			}
		}
		$this->headers = $headers;
	}

	private function getCurlHandle(string $url, array $headers = []): CurlHandle
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);


		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->verifySsl ? 2 : 0);

		$allHeaders = array_merge($this->headers, $headers);
		if (!empty($allHeaders)) {
			$formattedHeaders = $this->formatHeaders($allHeaders);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);
		}

		return $ch;
	}

	private function formatHeaders(array $headers): array
	{
		return array_map(
			fn($key, $value) => "$key: $value",
			array_keys($headers),
			$headers
		);
	}

	private function buildUrl(string $endpoint): string
	{
		$url = $this->baseUrl ? "{$this->baseUrl}/{$endpoint}" : $endpoint;
		return str_replace(["//", "://"], ["/", "://"], $url);
	}

	private function executeRequest(CurlHandle $ch): Response
	{
		curl_setopt($ch, CURLOPT_HEADER, true);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($response === false) {
			$error = curl_error($ch);
			curl_close($ch);
			throw new Exception("Curl error: $error");
		}

		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $headerSize);
		$body = substr($response, $headerSize);

		curl_close($ch);

		$headers = $this->parseHeaders($headers);

		return new Response($body, $httpCode, $headers);
	}

	private function parseHeaders(string $headerString): array
	{
		$headers = [];
		$lines = explode("\r\n", trim($headerString));

		foreach ($lines as $line) {
			if (strpos($line, ': ') !== false) {
				[$key, $value] = explode(': ', $line, 2);
				$headers[strtolower($key)] = $value;
			}
		}

		return $headers;
	}

	public function get(string $endpoint, array $headers = []): Response
	{
		if (empty($endpoint)) {
			throw new Exception("The 'get' method requires a valid endpoint.");
		}

		$url = $this->buildUrl($endpoint);
		$ch = $this->getCurlHandle($url, $headers);

		return $this->executeRequest($ch);
	}

	public function post(string $endpoint, $data = [], array $headers = []): Response
	{
		$url = $this->buildUrl($endpoint);
		$ch = $this->getCurlHandle($url, $headers);

		$contentType = 'application/x-www-form-urlencoded';
		foreach ($headers as $key => $value) {
			if (strtolower($key) === 'content-type') {
				$contentType = $value;
				break;
			}
		}

		switch ($contentType) {
			case 'application/json':
				$body = json_encode($data);
				break;
			case 'multipart/form-data':
				$body = $data;
				break;
			default:
				$body = http_build_query($data);
				break;
		}

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

		return $this->executeRequest($ch);
	}
}
