<?php

namespace Heseya;

use Exception;

class Silverbox
{
    /**
     * Server url.
     *
     * @var string
     */
    private $host;

    /**
     * Silverbox client name.
     *
     * @var string
     */
    private $client;

    /**
     * Api key.
     *
     * @var string
     */
    private $key;

    /**
     * @param string $host
     */
    public function __construct(string $host)
    {
        $this->host($host);
    }

    /**
     * Set host.
     *
     * @param string $host
     *
     * @return self
     */
    public function host(string $host): self
    {
        $this->host = rtrim($host, '/');

        return $this;
    }

    /**
     * Change credentials.
     *
     * @param string $clientName
     * @param string|null $key
     *
     * @return self
     */
    public function as(string $clientName, ?string $key = null): self
    {
        $this->client = $clientName;
        $this->key = $key;

        return $this;
    }

    /**
     * Get file absolute url.
     *
     * @param string $fileName
     *
     * @return string
     */
    public function url(string $fileName): string
    {
        return $this->clientUrl() . '/' . $fileName;
    }

    /**
     * Get file info.
     *
     * @param string $fileName
     *
     * @return mixed
     */
    public function info(string $fileName)
    {
        $response = $this->send($this->url($fileName), 'OPTIONS');

        return self::decodeJsonResponse($response['data']);
    }

    /**
     * Get file.
     *
     * @param string $fileName
     *
     * @return string
     */
    public function get(string $fileName): string
    {
        $response = $this->send($this->url($fileName));

        if ($json = json_decode($response['data'])) {
            return self::decodeJsonResponse($json);
        }

        return $response['data'];
    }

    /**
     * Upload a file.
     *
     * @param string $filePath
     *
     * @return mixed
     */
    public function upload(string $filePath)
    {
        $response = $this->send($this->clientUrl(), 'POST', [
            'file' => curl_file_create($filePath),
        ]);

        return self::decodeJsonResponse($response['data']);
    }

    /**
     * Delete a file.
     *
     * @param string $fileName
     *
     * @return bool
     */
    public function delete(string $fileName): bool
    {
        $response = $this->send($this->url($fileName), 'DELETE');

        if ($response['code'] == '204') {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function clientUrl(): string
    {
        return $this->host . '/' . $this->client;
    }

    /**
     * @param string $url       Request url
     * @param string $method    HTTP method in uppercase!
     * @param array|null $body
     *
     * @return array
     */
    private function send(string $url, string $method = 'GET', ?array $body = null): array
    {
        $connection = curl_init($url);

        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($connection, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->key,
        ]);

        if ($body) {
            curl_setopt($connection, CURLOPT_POSTFIELDS, $body);
        }

        $data = curl_exec($connection);

        if (curl_errno($connection)) {
            throw new Exception(curl_error($connection));
        }

        $code = curl_getinfo($connection, CURLINFO_HTTP_CODE);

        curl_close($connection);

        return [
            'code' => $code,
            'data' => $data,
        ];
    }

    /**
     * @param string $json
     *
     * @return mixed
     */
    private static function decodeJsonResponse(string $json)
    {
        $response = json_decode($json);

        if ($response === false || $response === null) {
            throw new Exception('JSON is not well formed');
        }

        if (isset($response->code)) {
            throw new Exception('API responded with error ' . $response->code . ' ' . $response->message);
        }

        return $response;
    }
}
