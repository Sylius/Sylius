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
use Sylius\Component\Core\Model\UserInterface;

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
        // create user with API role
        $user = $this->createUser(
            'api@example.com',
            'api',
            true,
            ['ROLE_API_ACCESS']
        );
        $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(['code' => 'administrator']));

        $manager->persist($user);
        $manager->flush();

        $clientManager = $this->getClientManager();

        /** @var Client $client */
        $client = $clientManager->createClient();
        $client->setRandomId('demo_client');
        $client->setSecret('secret_demo_client');
        $client->setAllowedGrantTypes(
            [
                OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                OAuth2::GRANT_TYPE_IMPLICIT,
                OAuth2::GRANT_TYPE_REFRESH_TOKEN,
                OAuth2::GRANT_TYPE_AUTH_CODE,
                OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
            ]
        );
        $clientManager->updateClient($client);
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool   $enabled
     * @param array  $roles
     * @param string $currency
     *
     * @return UserInterface
     */
    protected function createUser($email, $password, $enabled = true, array $roles = ['ROLE_USER'], $currency = 'EUR')
    {
        $canonicalizer = $this->get('sylius.user.canonicalizer');

        /* @var $user UserInterface */
        $user = $this->getUserFactory()->createNew();
        $customer = $this->getCustomerFactory()->createNew();
        $customer->setFirstname($this->faker->firstName);
        $customer->setLastname($this->faker->lastName);
        $customer->setCurrency($currency);
        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setUsernameCanonical($canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($canonicalizer->canonicalize($user->getEmail()));
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEnabled($enabled);

        $this->get('sylius.user.password_updater')->updatePassword($user);

        return $user;
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
        return 20;
    }
}
