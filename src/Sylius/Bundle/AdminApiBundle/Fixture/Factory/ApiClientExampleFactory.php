<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Fixture\Factory;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Sylius\Bundle\AdminApiBundle\Model\ClientInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ApiClientExampleFactory extends AbstractExampleFactory
{
    /**
     * @var ClientManagerInterface
     */
    private $clientManager;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param ClientManagerInterface $clientManager
     */
    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ClientInterface $client */
        $client = $this->clientManager->createClient();

        $client->setRandomId($options['random_id']);
        $client->setSecret($options['secret']);

        $client->setAllowedGrantTypes($options['allowed_grant_types']);

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('random_id', function (Options $options) {
                return $this->faker->unique()->randomNumber(8);
            })
            ->setDefault('secret', function (Options $options) {
                return $this->faker->uuid;
            })
            ->setDefault('allowed_grant_types', [])
            ->setAllowedTypes('allowed_grant_types', ['array'])
        ;
    }
}
