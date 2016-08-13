<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use FOS\ElasticaBundle\Elastica\Client;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
final class ElasticsearchChecker implements ElasticsearchCheckerInterface
{
    /**
     * @var Client
     */
    private $client;


    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function waitForPendingRequests($timeout = 5)
    {
        $end = time(true) + $timeout;

        do {
            $response = $this->client->request('_cat/pending_tasks')->getData();

            if (empty($response)) {
                break;
            }

            sleep(1);
        } while (time(true) < $end);
    }
}
