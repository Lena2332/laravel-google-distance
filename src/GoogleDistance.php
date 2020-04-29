<?php

namespace Pnlinh\GoogleDistance;

use Exception;
use GuzzleHttp\Client;
use Pnlinh\GoogleDistance\Contracts\DistanceContract;

class GoogleDistance implements DistanceContract
{
    /** @var string */
    private $apiUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    /** @var */
    private $apiKey;

    /** @var */
    private $origins;

    /** @var */
    private $destinations;

    /** @var */
    private $units;

    /**
     * DistanceApi constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey, $units = 'imperial')
    {
        $this->apiKey = $apiKey;
        $this->units = $units;
    }

    /**
     * Get API_KEY.
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get units.
     *
     * @return mixed
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Set units.
     *
     * @param $units
     *
     * @return \Pnlinh\GoogleDistance\GoogleDistance
     */
    public function setUnits($units): self
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Get origins.
     *
     * @return mixed
     */
    public function getOrigins()
    {
        return $this->origins;
    }

    /**
     * Set origins.
     *
     * @param $origins
     *
     * @return \Pnlinh\GoogleDistance\GoogleDistance
     */
    public function setOrigins($origins): self
    {
        $this->origins = $origins;

        return $this;
    }

    /**
     * Get destinations.
     *
     * @return mixed
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    /**
     * Set destinations.
     *
     * @param $destinations
     *
     * @return \Pnlinh\GoogleDistance\GoogleDistance
     */
    public function setDestinations($destinations): self
    {
        $this->destinations = $destinations;

        return $this;
    }

    /**
     * Caculate distance from origins to destinations.
     *
     * @param $origins
     * @param $destinations
     *
     * @return int
     */
    public function calculate($origins, $destinations, $units_override = false): int
    {
        $client = new Client();

        try {
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'units'        => $units_override !== false ? $units_override : $this->units,
                    'origins'      => $origins,
                    'destinations' => $destinations,
                    'key'          => $this->getApiKey(),
                    'random'       => random_int(1, 100),
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if (200 === $statusCode) {
                $responseData = json_decode($response->getBody()->getContents());

                if (isset($responseData->rows[0]->elements[0]->distance)) {
                    return $responseData->rows[0]->elements[0]->distance->value;
                }
            }

            return -1;
        } catch (Exception $e) {
            return -1;
        }
    }
}
