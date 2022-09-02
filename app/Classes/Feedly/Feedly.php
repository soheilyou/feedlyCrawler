<?php

namespace App\Classes\Feedly;

use App\Classes\Feedly\Exceptions\FeedlyException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Feedly
{
    /**
     * @var Repository|Application|mixed
     */
    private $host;
    /**
     * @var Repository|Application|mixed
     */
    private $token;
    /**
     * @var PendingRequest
     */
    protected $request;

    /**
     * Feedly constructor.
     */
    public function __construct()
    {
        $this->host = config("feedly.host");
        $this->token = config("feedly.token");
        $this->request = Http::withHeaders([
            "accept" => "application/json",
        ])->withToken($this->token);
    }

    /**
     * @param $url
     * @return string
     */
    protected function getFullUrl($url): string
    {
        return "$this->host/$url";
    }

    /**
     * @param $httpMethod
     * @param $url
     * @param $data
     * @return mixed
     * @throws FeedlyException
     */
    protected function makeRequest($httpMethod, $url, $data)
    {
        $response = $this->request->{$httpMethod}(
            $this->getFullUrl($url),
            $data
        );
        if ($response->failed()) {
            throw new FeedlyException($response->body());
        }
        return $response;
    }

    /**
     * @param $url
     * @param array|null $query
     * @return mixed
     * @throws FeedlyException
     */
    protected function get($url, array $query = null)
    {
        return $this->makeRequest("get", $url, $query);
    }

    /**
     * @param $url
     * @param array|null $query
     * @return mixed
     * @throws FeedlyException
     */
    protected function getJson($url, array $query = null)
    {
        return $this->get($url, $query)->json();
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws FeedlyException
     */
    protected function post($url, array $data = [])
    {
        return $this->makeRequest("post", $url, $data);
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws FeedlyException
     */
    protected function postJson($url, array $data = [])
    {
        return $this->post($url, $data)->json();
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws FeedlyException
     */
    protected function delete($url, array $data = [])
    {
        return $this->makeRequest("delete", $url, $data);
    }

    /**
     * @param array $items
     * @return mixed
     * @throws FeedlyException
     */
    public function addNewItems(array $items)
    {
        return $this->postJson("/api/services/crawler/add-new-items", $items);
    }
}
