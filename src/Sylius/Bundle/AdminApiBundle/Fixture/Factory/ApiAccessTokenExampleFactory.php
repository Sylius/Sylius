<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Fixture\Factory;

use Sylius\Bundle\AdminApiBundle\Model\AccessTokenInterface;
use Sylius\Bundle\AdminApiBundle\Model\ClientInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class ApiAccessTokenExampleFactory extends AbstractExampleFactory
{
    /** @var FactoryInterface */
    private $accessTokenFactory;

    /** @var UserRepositoryInterface */
    private $adminApiUserRepository;

    /** @var RepositoryInterface */
    private $clientRepository;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        FactoryInterface $accessTokenFactory,
        UserRepositoryInterface $adminApiUserRepository,
        RepositoryInterface $clientRepository
    ) {
        $this->accessTokenFactory = $accessTokenFactory;
        $this->adminApiUserRepository = $adminApiUserRepository;
        $this->clientRepository = $clientRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): AccessTokenInterface
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
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('user', LazyOption::randomOne($this->adminApiUserRepository))
            ->setAllowedTypes('user', ['string', UserInterface::class, 'null'])
            ->setNormalizer('user', function (Options $options, string $userEmail): UserInterface {
                $user = $this->adminApiUserRepository->findOneByEmail($userEmail);

                Assert::isInstanceOf($user, UserInterface::class);

                return $user;
            })
            ->setDefault('token', function (Options $options): string {
                return $this->faker->md5;
            })
            ->setDefault('client', LazyOption::randomOne($this->clientRepository))
            ->setAllowedTypes('client', ['string', ClientInterface::class, 'null'])
            ->setNormalizer('client', LazyOption::findOneBy($this->clientRepository, 'randomId'))
            ->setDefault('expires_at', null)
            ->setAllowedTypes('expires_at', ['null', \DateTimeInterface::class])
        ;
    }
}
