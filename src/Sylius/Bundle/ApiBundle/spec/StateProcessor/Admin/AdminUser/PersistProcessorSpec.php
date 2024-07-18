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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $this->beConstructedWith($persistProcessor, $passwordUpdater);
    }

    function it_does_not_process_delete_operation(
        AdminUserInterface $adminUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Delete();
        $passwordUpdater->updatePassword($adminUser)->shouldNotBeCalled();
        $persistProcessor->process($adminUser, $operation, [], [])->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$adminUser, $operation, [], []]);
    }

    function it_does_not_update_password_on_post_operation(
        AdminUserInterface $adminUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Post();
        $passwordUpdater->updatePassword($adminUser)->shouldNotBeCalled();
        $persistProcessor->process($adminUser, $operation, [], [])->shouldBeCalled();

        $this->process($adminUser, $operation, [], []);
    }

    function it_does_not_update_password_on_patch_operation(
        AdminUserInterface $adminUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Patch();
        $passwordUpdater->updatePassword($adminUser)->shouldNotBeCalled();
        $persistProcessor->process($adminUser, $operation, [], [])->shouldBeCalled();

        $this->process($adminUser, $operation, [], []);
    }

    function it_processes_put_operation(
        AdminUserInterface $adminUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Put();
        $passwordUpdater->updatePassword($adminUser)->shouldBeCalled();
        $persistProcessor->process($adminUser, $operation, [], [])->shouldBeCalled();

        $this->process($adminUser, $operation, [], []);
    }
}
