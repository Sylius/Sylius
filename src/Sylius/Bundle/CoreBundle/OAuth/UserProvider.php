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

namespace Sylius\Bundle\CoreBundle\OAuth;

use Doctrine\Persistence\ObjectManager;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider as BaseUserProvider;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface as SyliusUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 */
class UserProvider extends BaseUserProvider implements AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    public function __construct(
        string $supportedUserClass,
        private FactoryInterface $customerFactory,
        private FactoryInterface $userFactory,
        UserRepositoryInterface $userRepository,
        private FactoryInterface $oauthFactory,
        private RepositoryInterface $oauthRepository,
        private ObjectManager $userManager,
        CanonicalizerInterface $canonicalizer,
        private CustomerRepositoryInterface $customerRepository,
    ) {
        parent::__construct($supportedUserClass, $userRepository, $canonicalizer);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $oauth = $this->oauthRepository->findOneBy([
            'provider' => $response->getResourceOwner()->getName(),
            'identifier' => $response->getUsername(),
        ]);

        if ($oauth instanceof UserOAuthInterface) {
            return $oauth->getUser();
        }

        if (null !== $response->getEmail()) {
            $user = $this->userRepository->findOneByEmail($response->getEmail());
            if (null !== $user) {
                return $this->updateUserByOAuthUserResponse($user, $response);
            }

            return $this->createUserByOAuthUserResponse($response);
        }

        throw new UsernameNotFoundException('Email is null or not provided');
    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Ad-hoc creation of user.
     */
    private function createUserByOAuthUserResponse(UserResponseInterface $response): SyliusUserInterface
    {
        /** @var SyliusUserInterface $user */
        $user = $this->userFactory->createNew();

        $canonicalEmail = $this->canonicalizer->canonicalize($response->getEmail());

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $canonicalEmail]);

        if (null === $customer) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
        }

        $user->setCustomer($customer);

        // set default values taken from OAuth sign-in provider account
        if (null !== $email = $response->getEmail()) {
            $customer->setEmail($email);
        }

        if (null !== $name = $response->getFirstName()) {
            $customer->setFirstName($name);
        } elseif (null !== $realName = $response->getRealName()) {
            $customer->setFirstName($realName);
        }

        if (null !== $lastName = $response->getLastName()) {
            $customer->setLastName($lastName);
        }

        if (!$user->getUsername()) {
            $user->setUsername($response->getEmail() ?: $response->getNickname());
        }

        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPlainPassword(substr(sha1($response->getAccessToken()), 0, 10));

        $user->setEnabled(true);

        return $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Attach OAuth sign-in provider account to existing user.
     */
    private function updateUserByOAuthUserResponse(UserInterface $user, UserResponseInterface $response): SyliusUserInterface
    {
        /** @var SyliusUserInterface $user */
        Assert::isInstanceOf($user, SyliusUserInterface::class);

        /** @var UserOAuthInterface $oauth */
        $oauth = $this->oauthFactory->createNew();
        $oauth->setIdentifier($response->getUsername());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setRefreshToken($response->getRefreshToken());

        $user->addOAuthAccount($oauth);

        $this->userManager->persist($user);
        $this->userManager->flush();

        return $user;
    }
}
