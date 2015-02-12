<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;
use Sylius\Bundle\ApiBundle\Model\Client;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

/**
 * Api fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class LoadApiData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $clientManager = $this->getClientManager();

        /** @var Client $client */
        $client = $clientManager->createClient();
        $client->setRandomId('demo_client');
        $client->setSecret('secret_demo_client');
        $client->setAllowedGrantTypes(
            array(
                OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                OAuth2::GRANT_TYPE_IMPLICIT,
                OAuth2::GRANT_TYPE_REFRESH_TOKEN,
                OAuth2::GRANT_TYPE_AUTH_CODE,
                OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
            )
        );
        $clientManager->updateClient($client);
    }

    /**
     * @return ClientManagerInterface
     */
    private function getClientManager()
    {
        return $this->container->get('fos_oauth_server.client_manager.default');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
