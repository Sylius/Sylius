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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Account\ResetPassword;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;

final class ItemProviderSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_provides_reset_password_object_if_operation_is_patch(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Patch(class: ResetPassword::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $uriVariables = ['token' => 'TOKEN'];

        $this
            ->provide($operation, $uriVariables)
            ->shouldBeLike(new ResetPassword('TOKEN'));
    }

    function it_throws_an_exception_when_operation_class_is_not_reset_password(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_patch(
        Operation $operation,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation->getClass()->willReturn(ResetPassword::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Patch(class: ResetPassword::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_invalid_argument_exception_when_no_token_is_provided(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Patch(class: ResetPassword::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, ['token' => null]])
        ;
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, ['token' => '']])
        ;
    }
}
