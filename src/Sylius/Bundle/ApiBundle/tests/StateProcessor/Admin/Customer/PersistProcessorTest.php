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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Customer;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Customer\PersistProcessor;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $persist;

    private MockObject&PasswordUpdaterInterface $passwordUpdater;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->persist = $this->createMock(ProcessorInterface::class);
        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $this->persistProcessor = new PersistProcessor($this->persist, $this->passwordUpdater);
    }

    public function testDoesNotProcessDeleteOperation(): void
    {
        /** @var Customer|MockObject $customerMock */
        $customerMock = $this->createMock(Customer::class);
        /** @var ShopUser|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUser::class);

        $operation = new Delete();

        $this->passwordUpdater->expects(self::never())->method('updatePassword')->with($shopUserMock);

        $this->persist->expects(self::never())->method('process')->with($customerMock, $operation, [], []);

        self::expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process($customerMock, $operation, [], []);
    }

    public function testProcessesPostOperation(): void
    {
        /** @var Customer|MockObject $customerMock */
        $customerMock = $this->createMock(Customer::class);
        /** @var ShopUser|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUser::class);

        $operation = new Post();

        $customerMock->expects(self::once())->method('getUser')->willReturn($shopUserMock);

        $this->passwordUpdater->expects(self::once())->method('updatePassword')->with($shopUserMock);

        $this->persist->expects(self::once())->method('process')->with($customerMock, $operation, [], []);

        $this->persistProcessor->process($customerMock, $operation, [], []);
    }

    public function testProcessesPutOperation(): void
    {
        /** @var Customer|MockObject $customerMock */
        $customerMock = $this->createMock(Customer::class);
        /** @var ShopUser|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUser::class);

        $operation = new Put();

        $customerMock->expects(self::once())->method('getUser')->willReturn($shopUserMock);

        $this->passwordUpdater->expects(self::once())->method('updatePassword')->with($shopUserMock);

        $this->persist->expects(self::once())->method('process')->with($customerMock, $operation, [], []);

        $this->persistProcessor->process($customerMock, $operation, [], []);
    }

    public function testDoesNotUpdatePasswordOnPatchOperation(): void
    {
        /** @var Customer|MockObject $customerMock */
        $customerMock = $this->createMock(Customer::class);
        /** @var ShopUser|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUser::class);

        $operation = new Patch();

        $this->passwordUpdater->expects(self::never())->method('updatePassword')->with($shopUserMock);

        $this->persist->expects(self::once())->method('process')->with($customerMock, $operation, [], []);

        $this->persistProcessor->process($customerMock, $operation, [], []);
    }
}
