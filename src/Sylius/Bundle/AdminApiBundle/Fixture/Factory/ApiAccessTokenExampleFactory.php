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
use Sylius\Bundle\AdminApiBundle\Model\AccessTokenInterface;
use Sylius\Bundle\AdminApiBundle\Model\ClientInterface;
use Sylius\Bundle\AdminApiBundle\Model\UserInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ApiAccessTokenExampleFactory extends AbstractExampleFactory
{
    /**
     * @var FactoryInterface
     */
    private $accessTokenFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var RepositoryInterface
     */
    private $clientRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $accessTokenFactory
     * @param UserRepositoryInterface $userRepository
     * @param RepositoryInterface $clientRepository
     */
    public function __construct(
        FactoryInterface $accessTokenFactory,
        UserRepositoryInterface $userRepository,
        RepositoryInterface $clientRepository
    ) {
        $this->accessTokenFactory = $accessTokenFactory;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;

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

        /** @var AccessTokenInterface $accessToken */
        $accessToken = $this->accessTokenFactory->createNew();

        $accessToken->setClient($options['client']);
        $accessToken->setToken($options['token']);
        $accessToken->setUser($options['user']);

        if (isset($options['expires_at'])) {
            $accessToken->setExpiresAt($options['expires_at']);
        }

        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('user', LazyOption::randomOne($this->userRepository))
            ->setAllowedTypes('user', ['string', UserInterface::class, 'null'])
            ->setNormalizer('user', function (Options $options, $userEmail) {
                return $this->userRepository->findOneByEmail($userEmail);
            })
            ->setDefault('token', function (Options $options) {
                return $this->faker->md5;
            })
            ->setDefault('client', LazyOption::randomOne($this->clientRepository))
            ->setAllowedTypes('client', ['string', ClientInterface::class, 'null'])
            ->setNormalizer('client', LazyOption::findOneBy($this->clientRepository, 'randomId'))
            ->setDefault('expires_at', null)
            ->setAllowedTypes('expires_at', ['null', \DateTime::class])
        ;
    }
}
