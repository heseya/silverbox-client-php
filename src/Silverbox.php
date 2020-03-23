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
    protected $host;

    /**
     * Silverbox client name.
     *
     * @var string
     */
    protected $client;

    /**
     * Api key.
     *
     * @var string
     */
    protected $key;

    /**
     * @param string|null $host
     */
    public function __construct(?string $host = null)
    {
        if ($host !== null) {
            $this->host($host);
        }
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
     * Get file info.
     *
     * @param string $fileName
     *
     * @return self
     */
    public function url(string $fileName): string
    {
        return $this->clientUrl() . '/' . $fileName;
    }

    /**
     * Get file.
     *
     * @param string $fileName
     *
     * @return self
     */
    public function get(string $fileName): string
    {
        $connection = curl_init($this->url($fileName));

        curl_setopt($connection, CURLOPT_HEADER, false);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->key,
        ]);

        $output = curl_exec($connection);

        if (curl_errno($connection)) {
            throw new Exception(curl_error($connection));
        }

        curl_close($connection);

        if ($response = json_decode($output)) {
            throw new Exception('API responded with error ' . $response->code);
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function clientUrl(): string
    {
        return $this->host . '/' . $this->client;
    }
}
