<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\CoreBundle\OAuth;

use Doctrine\Persistence\ObjectManager;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OAuth\Checker\AlwaysVerifiedEmailChecker;
use Sylius\Bundle\CoreBundle\OAuth\Checker\EmailVerificationCheckerInterface;
use Sylius\Bundle\CoreBundle\OAuth\Exception\UnverifiedEmailException;
use Sylius\Bundle\CoreBundle\OAuth\UserProvider;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

#[AllowMockObjectsWithoutExpectations]
final class UserProviderTest extends TestCase
{
    private FactoryInterface&MockObject $customerFactory;

    private FactoryInterface&MockObject $userFactory;

    private MockObject&UserRepositoryInterface $userRepository;

    private FactoryInterface&MockObject $oauthFactory;

    private MockObject&ObjectManager $userManager;

    private CanonicalizerInterface&MockObject $canonicalizer;

    private CustomerRepositoryInterface&MockObject $customerRepository;

    private MockObject&RepositoryInterface $oauthRepository;

    private EmailVerificationCheckerInterface&MockObject $emailVerificationChecker;

    private UserProvider $provider;

    protected function setUp(): void
    {
        $this->customerFactory = $this->createMock(FactoryInterface::class);
        $this->userFactory = $this->createMock(FactoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->oauthFactory = $this->createMock(FactoryInterface::class);
        $this->oauthRepository = $this->createMock(RepositoryInterface::class);
        $this->userManager = $this->createMock(ObjectManager::class);
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->emailVerificationChecker = $this->createMock(EmailVerificationCheckerInterface::class);

        $this->provider = new UserProvider(
            ShopUserInterface::class,
            $this->customerFactory,
            $this->userFactory,
            $this->userRepository,
            $this->oauthFactory,
            $this->oauthRepository,
            $this->userManager,
            $this->canonicalizer,
            $this->customerRepository,
            $this->emailVerificationChecker,
        );
    }

    public function testImplementsOAuthAwareUserProviderInterface(): void
    {
        $this->assertInstanceOf(OAuthAwareUserProviderInterface::class, $this->provider);
    }

    public function testConnectAddsOAuthAccountToUser(): void
    {
        $user = $this->createMock(ShopUserInterface::class);
        $response = $this->createMock(UserResponseInterface::class);
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $oauth = $this->createMock(UserOAuthInterface::class);

        $response->method('getResourceOwner')->willReturn($resourceOwner);
        $response->method('getUsername')->willReturn('username');
        $response->method('getAccessToken')->willReturn('access_token');
        $response->method('getRefreshToken')->willReturn('refresh_token');
        $resourceOwner->method('getName')->willReturn('google');
        $response->method('getEmail')->willReturn(null);
        $this->oauthFactory->method('createNew')->willReturn($oauth);

        $oauth->expects($this->once())->method('setIdentifier')->with('username');
        $oauth->expects($this->once())->method('setProvider')->with('google');
        $oauth->expects($this->once())->method('setAccessToken')->with('access_token');
        $oauth->expects($this->once())->method('setRefreshToken')->with('refresh_token');

        $user->expects($this->once())->method('addOAuthAccount')->with($oauth);
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->provider->connect($user, $response);
    }

    public function testCreateUserByOAuthUserResponseCreatesNewUserWithCustomer(): void
    {
        $response = $this->createMock(UserResponseInterface::class);
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $user = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $oauth = $this->createMock(UserOAuthInterface::class);

        $response->method('getEmail')->willReturn('user@example.com');
        $response->method('getUsername')->willReturn('username');
        $response->method('getFirstName')->willReturn(null);
        $response->method('getRealName')->willReturn('Name');
        $response->method('getLastName')->willReturn(null);
        $response->method('getNickname')->willReturn('nickname');
        $response->method('getAccessToken')->willReturn('access_token');
        $response->method('getRefreshToken')->willReturn('refresh_token');
        $response->method('getResourceOwner')->willReturn($resourceOwner);
        $resourceOwner->method('getName')->willReturn('google');
        $this->canonicalizer->method('canonicalize')->willReturn('user@example.com');
        $this->customerRepository->method('findOneBy')->willReturn(null);
        $this->userFactory->method('createNew')->willReturn($user);
        $this->customerFactory->method('createNew')->willReturn($customer);
        $this->oauthFactory->method('createNew')->willReturn($oauth);

        $customer->expects($this->once())->method('setEmail')->with('user@example.com');
        $customer->expects($this->once())->method('setFirstName')->with('Name');
        $user->expects($this->once())->method('setCustomer')->with($customer);

        $user->method('getUsername')->willReturn(null);

        $user->expects($this->once())->method('setUsername')->with('user@example.com');
        $user->expects($this->once())->method('setPlainPassword')->with('2ff2dfe363');
        $user->expects($this->once())->method('setEnabled')->with(true);
        $user->expects($this->once())->method('addOAuthAccount')->with($oauth);

        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $result = (new \ReflectionClass(UserProvider::class))->getMethod('createUserByOAuthUserResponse');
        $createdUser = $result->invoke($this->provider, $response);

        $this->assertSame($user, $createdUser);
    }

    public function testThrowsExceptionWhenEmailNotProvided(): void
    {
        $this->expectException(UserNotFoundException::class);

        $response = $this->createMock(UserResponseInterface::class);
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $response->method('getResourceOwner')->willReturn($resourceOwner);
        $response->method('getUsername')->willReturn('username');
        $response->method('getEmail')->willReturn(null);
        $resourceOwner->method('getName')->willReturn('google');

        $this->oauthRepository
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->provider->loadUserByOAuthUserResponse($response);
    }

    public function testThrowsExceptionWhenUnsupportedUserIsUsed(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user = $this->createMock(UserInterface::class);
        $this->provider->refreshUser($user);
    }

    public function testLinksOAuthAccountToExistingUserWhenTheProviderConfirmedTheEmail(): void
    {
        $response = $this->createResponse();
        $user = $this->createMock(ShopUserInterface::class);

        $this->oauthRepository->method('findOneBy')->willReturn(null);
        $this->canonicalizer->method('canonicalize')->willReturn('user@example.com');
        $this->userRepository->method('findOneByEmail')->willReturn($user);
        $this->emailVerificationChecker->method('isEmailVerified')->with($response)->willReturn(true);
        $this->oauthFactory->method('createNew')->willReturn($this->createMock(UserOAuthInterface::class));

        $user->expects($this->once())->method('addOAuthAccount');
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->assertSame($user, $this->provider->loadUserByOAuthUserResponse($response));
    }

    public function testThrowsExceptionWhenTheProviderDidNotConfirmTheEmailOfAnExistingUser(): void
    {
        $response = $this->createResponse();
        $user = $this->createMock(ShopUserInterface::class);

        $this->oauthRepository->method('findOneBy')->willReturn(null);
        $this->canonicalizer->method('canonicalize')->willReturn('user@example.com');
        $this->userRepository->method('findOneByEmail')->willReturn($user);
        $this->emailVerificationChecker->method('isEmailVerified')->willReturn(false);

        $user->expects($this->never())->method('addOAuthAccount');
        $this->userManager->expects($this->never())->method('persist');
        $this->userManager->expects($this->never())->method('flush');

        $this->expectException(UnverifiedEmailException::class);

        $this->provider->loadUserByOAuthUserResponse($response);
    }

    public function testLinksOAuthAccountToExistingUserWithUnverifiedEmailWhenVerificationIsNotRequired(): void
    {
        $response = $this->createResponse();
        $user = $this->createMock(ShopUserInterface::class);

        $this->oauthRepository->method('findOneBy')->willReturn(null);
        $this->canonicalizer->method('canonicalize')->willReturn('user@example.com');
        $this->userRepository->method('findOneByEmail')->willReturn($user);
        $this->oauthFactory->method('createNew')->willReturn($this->createMock(UserOAuthInterface::class));

        $user->expects($this->once())->method('addOAuthAccount');

        $this->assertSame($user, $this->createProvider(new AlwaysVerifiedEmailChecker())->loadUserByOAuthUserResponse($response));
    }

    public function testCanonicalizesTheEmailBeforeLookingUpAnExistingUser(): void
    {
        $response = $this->createResponse('User@Example.COM');
        $user = $this->createMock(ShopUserInterface::class);

        $this->oauthRepository->method('findOneBy')->willReturn(null);
        $this->emailVerificationChecker->method('isEmailVerified')->willReturn(true);
        $this->oauthFactory->method('createNew')->willReturn($this->createMock(UserOAuthInterface::class));

        $this->canonicalizer
            ->expects($this->once())
            ->method('canonicalize')
            ->with('User@Example.COM')
            ->willReturn('user@example.com')
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->with('user@example.com')
            ->willReturn($user)
        ;

        $this->provider->loadUserByOAuthUserResponse($response);
    }

    public function testDoesNotCheckTheEmailWhenTheOAuthAccountIsAlreadyLinked(): void
    {
        $user = $this->createMock(ShopUserInterface::class);
        $oauth = $this->createMock(UserOAuthInterface::class);
        $oauth->method('getUser')->willReturn($user);

        $this->oauthRepository->method('findOneBy')->willReturn($oauth);

        $this->emailVerificationChecker->expects($this->never())->method('isEmailVerified');

        $this->assertSame($user, $this->provider->loadUserByOAuthUserResponse($this->createResponse()));
    }

    public function testDoesNotCheckTheEmailWhenAnAuthenticatedUserExplicitlyConnectsAnAccount(): void
    {
        $user = $this->createMock(ShopUserInterface::class);
        $this->oauthFactory->method('createNew')->willReturn($this->createMock(UserOAuthInterface::class));

        $this->emailVerificationChecker->expects($this->never())->method('isEmailVerified');
        $user->expects($this->once())->method('addOAuthAccount');

        $this->provider->connect($user, $this->createResponse());
    }

    private function createResponse(string $email = 'user@example.com', string $resourceOwnerName = 'google'): MockObject&UserResponseInterface
    {
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $resourceOwner->method('getName')->willReturn($resourceOwnerName);

        $response = $this->createMock(UserResponseInterface::class);
        $response->method('getResourceOwner')->willReturn($resourceOwner);
        $response->method('getUsername')->willReturn('username');
        $response->method('getEmail')->willReturn($email);
        $response->method('getAccessToken')->willReturn('access_token');
        $response->method('getRefreshToken')->willReturn('refresh_token');

        return $response;
    }

    private function createProvider(EmailVerificationCheckerInterface $emailVerificationChecker): UserProvider
    {
        return new UserProvider(
            ShopUserInterface::class,
            $this->customerFactory,
            $this->userFactory,
            $this->userRepository,
            $this->oauthFactory,
            $this->oauthRepository,
            $this->userManager,
            $this->canonicalizer,
            $this->customerRepository,
            $emailVerificationChecker,
        );
    }
}
