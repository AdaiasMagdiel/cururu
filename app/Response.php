<?php

namespace AdaiasMagdiel\Cururu;

class Response
{
	private string $body;
	private int    $statusCode;
	private array  $headers;

	public function __construct(string $body, int $statusCode, array $headers)
	{
		$this->body = $body;
		$this->statusCode = $statusCode;
		$this->headers = $headers;
	}

	public function getJson(): ?array
	{
		$json = json_decode($this->body, true);
		return (json_last_error() === JSON_ERROR_NONE) ? $json : null;
	}

	public function getContent(): string
	{
		return $this->body;
	}

	public function getText(): string
	{
		return (string) $this->body;
	}

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	public function getHeader(string $key): ?string
	{
		$key = ucfirst(strtolower($key));
		return $this->headers[$key] ?? null;
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function isSuccessful(): bool
	{
		return $this->statusCode >= 200 && $this->statusCode < 300;
	}
}
