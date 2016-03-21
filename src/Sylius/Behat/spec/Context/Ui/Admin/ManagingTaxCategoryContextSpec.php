<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\Admin\ManagingTaxCategoryContext;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @mixin ManagingTaxCategoryContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ManagingTaxCategoryContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $taxCategoryIndexPage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->beConstructedWith($sharedStorage, $taxCategoryIndexPage, $notificationAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingTaxCategoryContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_deletes_a_tax_cateogory(
        IndexPageInterface $taxCategoryIndexPage,
        SharedStorageInterface $sharedStorage,
        TaxCategoryInterface $taxCategory
    ) {
        $taxCategory->getCode()->willReturn('alcohol');

        $taxCategoryIndexPage->deleteResourceOnPage(['code' => 'alcohol'])->shouldBeCalled();
        $taxCategoryIndexPage->open()->shouldBeCalled();
        $sharedStorage->set('tax_category', $taxCategory)->shouldBeCalled();

        $this->iDeletedTaxCategory($taxCategory);
    }

    function it_checks_if_a_tax_category_does_not_exist_in_the_registry_anymore(
        TaxCategoryInterface $taxCategory,
        IndexPageInterface $taxCategoryIndexPage
    ) {
        $taxCategory->getCode()->willReturn('alcohol');
        $taxCategoryIndexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(false);

        $this->thisTaxCategoryShouldNoLongerExistInTheRegistry($taxCategory);
    }

    function it_throws_an_exception_if_a_tax_category_still_exist_in_the_registry(
        TaxCategoryInterface $taxCategory,
        IndexPageInterface $taxCategoryIndexPage
    ) {
        $taxCategory->getCode()->willReturn('alcohol');
        $taxCategoryIndexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(true);

        $this
            ->shouldThrow(new \InvalidArgumentException("Tax category with code alcohol exists but should not"))
            ->during('thisTaxCategoryShouldNoLongerExistInTheRegistry', [$taxCategory])
        ;
    }

    function it_checks_if_a_resource_was_successfully_deleted(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyDeletedFor('tax_category')->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccessfulDeletion();
    }

    function it_throws_an_exception_if_the_page_does_not_have_success_message(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(false);

        $this->shouldThrow(new \InvalidArgumentException('Message type is not positive'))->during('iShouldBeNotifiedAboutSuccessfulDeletion', []);
    }

    function it_throws_an_exception_if_the_message_on_a_page_is_not_related_to_deletion(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyDeletedFor('tax_category')->willReturn(false);

        $this->shouldThrow(new \InvalidArgumentException('Successful deletion message does not appear'))->during('iShouldBeNotifiedAboutSuccessfulDeletion', []);
    }
}
