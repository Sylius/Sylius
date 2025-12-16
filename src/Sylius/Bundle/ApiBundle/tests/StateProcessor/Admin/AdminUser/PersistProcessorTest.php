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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser\PersistProcessor;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private MockObject&PasswordUpdaterInterface $passwordUpdater;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $this->persistProcessor = new PersistProcessor($this->processor, $this->passwordUpdater);
    }

    public function testDoesNotProcessDeleteOperation(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);

        $operation = new Delete();

        $this->passwordUpdater->expects(self::never())->method('updatePassword')->with($adminUserMock);

        $this->processor->expects(self::never())->method('process')->with($adminUserMock, $operation, [], []);

        $this->expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process($adminUserMock, $operation, [], []);
    }

    public function testDoesNotUpdatePasswordOnPostOperation(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);

        $operation = new Post();

        $this->passwordUpdater->expects(self::never())->method('updatePassword')->with($adminUserMock);

        $this->processor->expects(self::once())->method('process')->with($adminUserMock, $operation, [], []);

        $this->persistProcessor->process($adminUserMock, $operation, [], []);
    }

    public function testDoesNotUpdatePasswordOnPatchOperation(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);

        $operation = new Patch();

        $this->passwordUpdater->expects(self::never())->method('updatePassword')->with($adminUserMock);

        $this->processor->expects(self::once())->method('process')->with($adminUserMock, $operation, [], []);

        $this->persistProcessor->process($adminUserMock, $operation, [], []);
    }

    public function testProcessesPutOperation(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);

        $operation = new Put();

        $this->passwordUpdater->expects(self::once())->method('updatePassword')->with($adminUserMock);

        $this->processor->expects(self::once())->method('process')->with($adminUserMock, $operation, [], []);

        $this->persistProcessor->process($adminUserMock, $operation, [], []);
    }
}
