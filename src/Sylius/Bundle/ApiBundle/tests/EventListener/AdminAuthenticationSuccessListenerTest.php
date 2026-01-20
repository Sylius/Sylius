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

use ApiPlatform\Metadata\IriConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventListener\AdminAuthenticationSuccessListener;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;

final class AdminAuthenticationSuccessListenerTest extends TestCase
{
    private IriConverterInterface&MockObject $iriConverter;

    private AdminUserInterface&MockObject $adminUser;

    private AdminAuthenticationSuccessListener $adminAuthenticationSuccessListener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->adminAuthenticationSuccessListener = new AdminAuthenticationSuccessListener($this->iriConverter);
        $this->adminUser = $this->createMock(AdminUserInterface::class);
    }

    public function testAddsAdminsToAdminAuthenticationTokenResponse(): void
    {
        $event = new AuthenticationSuccessEvent([], $this->adminUser, new Response());

        $this->iriConverter->expects(self::once())->method('getIriFromResource')->with($this->adminUser);

        $this->adminAuthenticationSuccessListener->onAuthenticationSuccessResponse($event);
    }

    public function testDoesNotAddAnythingToShopAuthenticationTokenResponse(): void
    {
        /** @var ShopUserInterface&MockObject $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);

        $event = new AuthenticationSuccessEvent([], $shopUser, new Response());

        $this->iriConverter->expects(self::never())->method('getIriFromResource')->with($this->adminUser);

        $this->adminAuthenticationSuccessListener->onAuthenticationSuccessResponse($event);
    }
}
