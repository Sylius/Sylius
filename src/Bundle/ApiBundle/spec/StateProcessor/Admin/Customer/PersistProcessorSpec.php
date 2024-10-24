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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Customer;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUser;
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
        Customer $customer,
        ShopUser $shopUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Delete();
        $passwordUpdater->updatePassword($shopUser)->shouldNotBeCalled();
        $persistProcessor->process($customer, $operation, [], [])->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$customer, $operation, [], []]);
    }

    function it_processes_post_operation(
        Customer $customer,
        ShopUser $shopUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Post();
        $customer->getUser()->willReturn($shopUser);
        $passwordUpdater->updatePassword($shopUser)->shouldBeCalled();
        $persistProcessor->process($customer, $operation, [], [])->shouldBeCalled();

        $this->process($customer, $operation, [], []);
    }

    function it_processes_put_operation(
        Customer $customer,
        ShopUser $shopUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Put();
        $customer->getUser()->willReturn($shopUser);
        $passwordUpdater->updatePassword($shopUser)->shouldBeCalled();
        $persistProcessor->process($customer, $operation, [], [])->shouldBeCalled();

        $this->process($customer, $operation, [], []);
    }

    function it_does_not_update_password_on_patch_operation(
        Customer $customer,
        ShopUser $shopUser,
        ProcessorInterface $persistProcessor,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $operation = new Patch();
        $passwordUpdater->updatePassword($shopUser)->shouldNotBeCalled();
        $persistProcessor->process($customer, $operation, [], [])->shouldBeCalled();

        $this->process($customer, $operation, [], []);
    }
}
