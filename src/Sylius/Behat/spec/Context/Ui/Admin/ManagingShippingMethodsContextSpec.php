<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\Admin\ManagingShippingMethodsContext;
use Sylius\Behat\Page\Admin\ShippingMethod\IndexPageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\ShowPageInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin ManagingShippingMethodsContext
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ManagingShippingMethodsContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        ShowPageInterface $shippingMethodShowPage,
        IndexPageInterface $shippingMethodIndexPage
    ) {
        $this->beConstructedWith($sharedStorage, $shippingMethodShowPage, $shippingMethodIndexPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingShippingMethodsContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_deletes_given_shipping_method(
        ShowPageInterface $shippingMethodShowPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getId()->willReturn(5);
        $shippingMethodShowPage->open(['id' => 5])->shouldBeCalled();
        $shippingMethodShowPage->pressDelete()->shouldBeCalled();

        $this->iTryToDeleteShippingMethod($shippingMethod);
    }

    function it_sets_error_flash_if_method_is_in_use(ShowPageInterface $shippingMethodShowPage)
    {
        $shippingMethodShowPage->flashContainsMessage(
            'Cannot delete, the shipping method is in use.'
        )->willReturn(true);

        $this->iShouldBeNotifiedItIsUsed();
    }

    function it_throws_exception_when_flash_was_not_set(ShowPageInterface $shippingMethodShowPage)
    {
        $shippingMethodShowPage->flashContainsMessage(
            'Cannot delete, the shipping method is in use.'
        )->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeNotifiedItIsUsed');
    }

    function it_checks_whether_a_shipping_method_was_removed(
        IndexPageInterface $shippingMethodIndexPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethodIndexPage->isOpen()->willReturn(true);
        $shippingMethod->getName()->willReturn('UPS Express');

        $shippingMethodIndexPage->isThereShippingMethodNamed('UPS Express')->willReturn(false);

        $this->shippingMethodShouldBeRemoved($shippingMethod);
    }

    function it_throws_exception_when_a_shipping_method_was_not_removed_when_it_should_have_been(
        IndexPageInterface $shippingMethodIndexPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getName()->willReturn('UPS Express');

        $shippingMethodIndexPage->isOpen()->willReturn(true);
        $shippingMethodIndexPage->isThereShippingMethodNamed('UPS Express')->willReturn(true);

        $this->shouldThrow(NotEqualException::class)->during('shippingMethodShouldBeRemoved', [$shippingMethod]);
    }

    function it_throws_exception_if_there_was_no_redirect_to_index_after_successful_deletion_of_a_shipping_method(
        IndexPageInterface $shippingMethodIndexPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getName()->willReturn('UPS Express');

        $shippingMethodIndexPage->isOpen()->willReturn(false);
        $shippingMethodIndexPage->isThereShippingMethodNamed('UPS Express')->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('shippingMethodShouldBeRemoved', [$shippingMethod]);
    }

    function it_checks_whether_a_shipping_method_was_not_removed(
        ShowPageInterface $shippingMethodShowPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getName()->willReturn('UPS Express');
        $shippingMethod->getId()->willReturn(5);

        $shippingMethodShowPage->isOpen(['id' => 5])->willReturn(true);
        $shippingMethodShowPage->verify(['id' => 5])->willNotThrow(UnexpectedPageException::class);

        $this->shippingMethodShouldNotBeRemoved($shippingMethod);
    }

    function it_throws_exception_when_a_shipping_method_was_removed_when_it_should_not(
        ShowPageInterface $shippingMethodShowPage,
        IndexPageInterface $shippingMethodIndexPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getName()->willReturn('UPS Express');
        $shippingMethod->getId()->willReturn(5);

        $shippingMethodShowPage->isOpen(['id' => 5])->willReturn(true);
        $shippingMethodIndexPage->isThereShippingMethodNamed('UPS Express')->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('shippingMethodShouldNotBeRemoved', [$shippingMethod]);
    }

    function it_checks_if_there_was_no_redirect_to_index_after_unsuccessful_deletion_of_a_shipping_method(
        ShowPageInterface $shippingMethodShowPage,
        IndexPageInterface $shippingMethodIndexPage,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getName()->willReturn('UPS Express');
        $shippingMethod->getId()->willReturn(5);

        $shippingMethodShowPage->isOpen(['id' => 5])->willReturn(false);
        $shippingMethodIndexPage->isThereShippingMethodNamed('UPS Express')->willReturn(true);

        $this->shouldThrow(NotEqualException::class)->during('shippingMethodShouldNotBeRemoved', [$shippingMethod]);
    }
}
