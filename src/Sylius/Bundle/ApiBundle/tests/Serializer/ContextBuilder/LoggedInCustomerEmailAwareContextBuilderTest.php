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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LoggedInCustomerEmailAwareContextBuilder;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class LoggedInCustomerEmailAwareContextBuilderTest extends TestCase
{
    private MockObject&SerializerContextBuilderInterface $decoratedContextBuilder;

    private MockObject&UserContextInterface $userContext;

    private LoggedInCustomerEmailAwareContextBuilder $loggedInCustomerEmailAwareContextBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedContextBuilder = $this->createMock(SerializerContextBuilderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->loggedInCustomerEmailAwareContextBuilder = new LoggedInCustomerEmailAwareContextBuilder(
            $this->decoratedContextBuilder,
            LoggedInCustomerEmailAware::class,
            'email',
            $this->userContext,
        );
    }

    public function testDoesNotAddEmailToContactAwareCommandIfProvided(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;
        $requestMock->expects(self::once())->method('toArray')->willReturn([
            'email' => 'email@example.com',
            'message' => 'message',
        ]);

        $this->userContext->expects(self::never())->method('getUser');

        self::assertSame(
            ['input' => ['class' => SendContactRequest::class]],
            $this->loggedInCustomerEmailAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }

    public function testEarlyReturnsContactAwareCommandIfAdminUserProvided(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]]);

        $requestMock->expects(self::once())->method('toArray')->willReturn(['message' => 'message']);

        $this->userContext->expects(self::once())->method('getUser')->willReturn($adminUserMock);

        self::assertSame(
            ['input' => ['class' => SendContactRequest::class]],
            $this->loggedInCustomerEmailAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }

    public function testAddsNothingForVisitor(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]]);

        $requestMock->expects(self::once())->method('toArray')->willReturn(['message' => 'message']);

        $this->userContext->expects(self::once())->method('getUser')->willReturn(null);

        self::assertSame(
            ['input' => ['class' => SendContactRequest::class]],
            $this->loggedInCustomerEmailAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }

    public function testAddsEmailIfNotProvidedAndTheUserIsLoggedIn(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]]);

        $requestMock->expects(self::once())->method('toArray')->willReturn(['message' => 'message']);

        $this->userContext->expects(self::atLeastOnce())->method('getUser')->willReturn($shopUserMock);

        $shopUserMock->expects(self::atLeastOnce())->method('getCustomer')->willReturn($customerMock);

        $customerMock->expects(self::once())->method('getEmail')->willReturn('email@example.com');

        self::assertSame([
            'input' => ['class' => SendContactRequest::class],
            AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                SendContactRequest::class => ['email' => 'email@example.com'],
            ],
        ], $this->loggedInCustomerEmailAwareContextBuilder
            ->createFromRequest($requestMock, true, []))
        ;
    }

    public function testWorksOnlyForLoggedInCustomerEmailIfNotSetInterface(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]]);

        $requestMock->expects(self::never())->method('toArray');

        self::assertSame(
            ['input' => ['class' => \stdClass::class]],
            $this->loggedInCustomerEmailAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }
}
