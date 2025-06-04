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
use Sylius\Bundle\ApiBundle\Attribute\ShopUserIdAware;
use Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LoggedInShopUserIdAwareContextBuilder;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class LoggedInShopUserIdAwareContextBuilderTest extends TestCase
{
    private MockObject&SerializerContextBuilderInterface $decoratedContextBuilder;

    private MockObject&UserContextInterface $userContext;

    private LoggedInShopUserIdAwareContextBuilder $loggedInShopUserIdAwareContextBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedContextBuilder = $this->createMock(SerializerContextBuilderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->loggedInShopUserIdAwareContextBuilder = new LoggedInShopUserIdAwareContextBuilder(
            $this->decoratedContextBuilder,
            ShopUserIdAware::class,
            'shopUserId',
            $this->userContext,
        );
    }

    public function testSetsShopUserIdAsAConstructorArgument(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => RequestShopUserVerification::class]]);

        $this->userContext->expects(self::atLeastOnce())->method('getUser')->willReturn($shopUserMock);

        $shopUserMock->expects(self::once())->method('getId')->willReturn(11);

        self::assertSame([
            'input' => ['class' => RequestShopUserVerification::class],
            AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                RequestShopUserVerification::class => ['shopUserId' => 11],
            ],
        ], $this->loggedInShopUserIdAwareContextBuilder
            ->createFromRequest($requestMock, true, []))
        ;
    }

    public function testDoesNothingIfThereIsNoInputClass(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn([]);

        $this->userContext->expects(self::never())->method('getUser');

        self::assertSame(
            [],
            $this->loggedInShopUserIdAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }

    public function testDoesNothingIfInputClassIsNoChannelAware(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]]);

        $this->userContext->expects(self::never())->method('getUser');

        self::assertSame(
            ['input' => ['class' => \stdClass::class]],
            $this->loggedInShopUserIdAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }

    public function testDoesNothingIfThereIsNoLoggedInShopUser(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder
            ->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => RequestShopUserVerification::class]])
        ;

        $this->userContext
            ->expects(self::once())
            ->method('getUser')
            ->willReturn(null)
        ;

        self::assertSame(
            ['input' => ['class' => RequestShopUserVerification::class]],
            $this->loggedInShopUserIdAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }
}
