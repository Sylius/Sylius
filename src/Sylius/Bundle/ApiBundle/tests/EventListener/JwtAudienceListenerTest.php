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

namespace Tests\Sylius\Bundle\ApiBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ApiBundle\EventListener\JwtAudienceListener;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtAudienceListenerTest extends TestCase
{
    private const ADMIN_AUDIENCE = 'sylius-api-admin';

    private const SHOP_AUDIENCE = 'sylius-api-shop';

    /** @var array<string, array{audience: string, principal: class-string}> */
    private const FIREWALL_EXPECTATIONS = [
        'api_admin' => ['audience' => self::ADMIN_AUDIENCE, 'principal' => AdminUserInterface::class],
        'api_shop' => ['audience' => self::SHOP_AUDIENCE, 'principal' => ShopUserInterface::class],
    ];

    private FirewallMap&MockObject $firewallMap;

    private RequestStack $requestStack;

    private LoggerInterface&MockObject $logger;

    private JwtAudienceListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->firewallMap = $this->createMock(FirewallMap::class);
        $this->requestStack = new RequestStack();
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->listener = new JwtAudienceListener(
            $this->firewallMap,
            $this->requestStack,
            $this->logger,
            self::FIREWALL_EXPECTATIONS,
        );
    }

    public function testStampsAdminClaimsOnTokenCreatedForAdminUser(): void
    {
        $event = new JWTCreatedEvent(['username' => 'shared@example.com'], $this->createMock(AdminUserInterface::class));

        $this->listener->onJwtCreated($event);

        self::assertSame([
            'username' => 'shared@example.com',
            'aud' => self::ADMIN_AUDIENCE,
            'principal_type' => AdminUserInterface::class,
        ], $event->getData());
    }

    public function testStampsShopClaimsOnTokenCreatedForShopUser(): void
    {
        $event = new JWTCreatedEvent(['username' => 'shared@example.com'], $this->createMock(ShopUserInterface::class));

        $this->listener->onJwtCreated($event);

        self::assertSame([
            'username' => 'shared@example.com',
            'aud' => self::SHOP_AUDIENCE,
            'principal_type' => ShopUserInterface::class,
        ], $event->getData());
    }

    public function testDoesNotStampAnyClaimsForUnsupportedUser(): void
    {
        $event = new JWTCreatedEvent(['username' => 'other@example.com'], $this->createMock(UserInterface::class));

        $this->listener->onJwtCreated($event);

        self::assertSame(['username' => 'other@example.com'], $event->getData());
    }

    public function testAcceptsTokenWhoseAudienceClaimHasBeenDecodedAsAnArray(): void
    {
        $this->useFirewall('api_admin');

        $event = new JWTDecodedEvent([
            'aud' => [self::ADMIN_AUDIENCE],
            'principal_type' => AdminUserInterface::class,
        ]);

        $this->logger->expects(self::never())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertTrue($event->isValid());
    }

    public function testAcceptsTokenWhoseAudienceClaimHasBeenDecodedAsAString(): void
    {
        $this->useFirewall('api_shop');

        $event = new JWTDecodedEvent([
            'aud' => self::SHOP_AUDIENCE,
            'principal_type' => ShopUserInterface::class,
        ]);

        $this->logger->expects(self::never())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertTrue($event->isValid());
    }

    public function testAcceptsTokenWhenAStaleRequestIsLeftAtTheBottomOfTheRequestStack(): void
    {
        $staleRequest = new Request();
        $this->requestStack->push($staleRequest);

        $apiRequest = Request::create('/api/v2/shop/orders/nAWw2945');
        $this->requestStack->push($apiRequest);

        $this->firewallMap
            ->method('getFirewallConfig')
            ->willReturnCallback(fn (Request $request) => new FirewallConfig(
                $request === $apiRequest ? 'api_shop' : 'shop',
                'security.user_checker',
            ))
        ;

        $event = new JWTDecodedEvent([
            'aud' => [self::SHOP_AUDIENCE],
            'principal_type' => ShopUserInterface::class,
        ]);

        $this->logger->expects(self::never())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertTrue($event->isValid());
    }

    public function testRejectsShopTokenSentToTheAdminFirewall(): void
    {
        $this->useFirewall('api_admin');

        $event = new JWTDecodedEvent([
            'aud' => self::SHOP_AUDIENCE,
            'principal_type' => ShopUserInterface::class,
        ]);

        $this->logger->expects(self::once())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertFalse($event->isValid());
    }

    public function testRejectsAdminTokenSentToTheShopFirewall(): void
    {
        $this->useFirewall('api_shop');

        $event = new JWTDecodedEvent([
            'aud' => self::ADMIN_AUDIENCE,
            'principal_type' => AdminUserInterface::class,
        ]);

        $this->logger->expects(self::once())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertFalse($event->isValid());
    }

    public function testRejectsTokenWithMatchingAudienceButMismatchedPrincipalType(): void
    {
        $this->useFirewall('api_admin');

        $event = new JWTDecodedEvent([
            'aud' => self::ADMIN_AUDIENCE,
            'principal_type' => ShopUserInterface::class,
        ]);

        $this->logger->expects(self::once())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertFalse($event->isValid());
    }

    public function testRejectsTokenIssuedBeforeTheAudienceClaimsHaveBeenIntroduced(): void
    {
        $this->useFirewall('api_admin');

        $event = new JWTDecodedEvent(['username' => 'shared@example.com']);

        $this->logger->expects(self::once())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertFalse($event->isValid());
    }

    public function testRejectsTokenWhenTheRequestIsHandledByAnUnknownFirewall(): void
    {
        $this->useFirewall('main');

        $event = new JWTDecodedEvent([
            'aud' => self::ADMIN_AUDIENCE,
            'principal_type' => AdminUserInterface::class,
        ]);

        $this->logger->expects(self::once())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertFalse($event->isValid());
    }

    public function testRejectsTokenWhenThereIsNoRequest(): void
    {
        $this->firewallMap->expects(self::never())->method('getFirewallConfig');

        $event = new JWTDecodedEvent([
            'aud' => self::ADMIN_AUDIENCE,
            'principal_type' => AdminUserInterface::class,
        ]);

        $this->logger->expects(self::once())->method('warning');

        $this->listener->onJwtDecoded($event);

        self::assertFalse($event->isValid());
    }

    public function testUsesTheFirewallExpectationsGivenInConfiguration(): void
    {
        $this->useFirewall('custom_admin_api');

        $listener = new JwtAudienceListener($this->firewallMap, $this->requestStack, $this->logger, [
            'custom_admin_api' => ['audience' => self::ADMIN_AUDIENCE, 'principal' => AdminUserInterface::class],
        ]);

        $event = new JWTDecodedEvent([
            'aud' => self::ADMIN_AUDIENCE,
            'principal_type' => AdminUserInterface::class,
        ]);

        $this->logger->expects(self::never())->method('warning');

        $listener->onJwtDecoded($event);

        self::assertTrue($event->isValid());
    }

    private function useFirewall(string $name): void
    {
        $this->requestStack->push(new Request());

        $this->firewallMap
            ->method('getFirewallConfig')
            ->willReturn(new FirewallConfig($name, 'security.user_checker'))
        ;
    }
}
