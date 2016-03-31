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
use Sylius\Behat\Page\Admin\TaxCategory\CreatePageInterface;
use Sylius\Behat\Page\Admin\TaxCategory\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @mixin ManagingTaxCategoryContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ManagingTaxCategoryContextSpec extends ObjectBehavior
{
    function let(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        NotificationCheckerInterface $notificationValidator
    ) {
        $this->beConstructedWith(
            $indexPage,
            $createPage,
            $updatePage,
            $notificationValidator
        );
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
        IndexPageInterface $indexPage,
        TaxCategoryInterface $taxCategory
    ) {
        $taxCategory->getCode()->willReturn('alcohol');

        $indexPage->deleteResourceOnPage(['code' => 'alcohol'])->shouldBeCalled();
        $indexPage->open()->shouldBeCalled();

        $this->iDeletedTaxCategory($taxCategory);
    }

    function it_checks_if_a_tax_category_does_not_exist_in_the_registry_anymore(
        TaxCategoryInterface $taxCategory,
        IndexPageInterface $indexPage
    ) {
        $taxCategory->getCode()->willReturn('alcohol');
        $indexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(false);

        $this->thisTaxCategoryShouldNoLongerExistInTheRegistry($taxCategory);
    }

    function it_throws_an_exception_if_a_tax_category_still_exist_in_the_registry(
        TaxCategoryInterface $taxCategory,
        IndexPageInterface $indexPage
    ) {
        $taxCategory->getCode()->willReturn('alcohol');
        $indexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(true);

        $this
            ->shouldThrow(new \InvalidArgumentException("Tax category with code alcohol exists but should not."))
            ->during('thisTaxCategoryShouldNoLongerExistInTheRegistry', [$taxCategory])
        ;
    }

    function it_checks_if_a_resource_was_successfully_deleted(NotificationCheckerInterface $notificationValidator)
    {
        $notificationValidator->checkDeletionNotification('tax_category')->shouldBeCalled();

        $this->iShouldBeNotifiedAboutSuccessfulDeletion();
    }

    function it_opens_a_create_page(CreatePageInterface $createPage)
    {
        $createPage->open()->shouldBeCalled();

        $this->iWantToCreateNewTaxCategory();
    }

    function it_specifies_tax_category_code(CreatePageInterface $createPage)
    {
        $createPage->specifyCode('food_and_beverage')->shouldBeCalled();

        $this->iSpecifyItsCodeAs('food_and_beverage');
    }

    function it_specifies_tax_category_name(CreatePageInterface $createPage)
    {
        $createPage->nameIt('Food and Beverage')->shouldBeCalled();

        $this->iNameIt('Food and Beverage');
    }

    function it_specifies_tax_category_description(CreatePageInterface $createPage)
    {
        $createPage->describeItAs('Best stuff to get wasted in town')->shouldBeCalled();

        $this->iDescribeItAs('Best stuff to get wasted in town');
    }

    function it_creates_a_resource(CreatePageInterface $createPage)
    {
        $createPage->create()->shouldBeCalled();

        $this->iAddIt();
    }

    function it_asserts_if_a_resource_was_successfully_created(IndexPageInterface $indexPage) {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['name' => 'Food and Beverage'])->willReturn(true);

        $this->theTaxCategoryShouldAppearInTheRegistry('Food and Beverage');
    }

    function it_throws_an_exception_if_resource_does_not_have_proper_fields_filled(IndexPageInterface $indexPage) {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['name' => 'Food and Beverage'])->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Tax category with name Food and Beverage has not been found.'))
            ->during('theTaxCategoryShouldAppearInTheRegistry', ['Food and Beverage'])
        ;
    }

    function it_checks_if_a_resource_was_successfully_created(NotificationCheckerInterface $notificationValidator)
    {
        $notificationValidator->checkCreationNotification('tax_category')->shouldBeCalled();

        $this->iShouldBeNotifiedItHasBeenSuccessfulCreation();
    }

    function it_opens_an_update_page(UpdatePageInterface $updatePage, TaxCategoryInterface $taxCategory)
    {
        $taxCategory->getId()->willReturn(1);
        $updatePage->open(['id' => 1])->shouldBeCalled();

        $this->iWantToModifyTaxCategory($taxCategory);
    }

    function it_checks_if_the_code_cannot_be_changed(UpdatePageInterface $updatePage)
    {
        $updatePage->isCodeDisabled()->willReturn(true);

        $this->theCodeFieldShouldBeDisabled();
    }

    function it_throws_an_exception_if_the_code_field_is_not_immutable(UpdatePageInterface $updatePage)
    {
        $updatePage->isCodeDisabled()->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Code should be immutable, but it does not.'))
            ->during('theCodeFieldShouldBeDisabled')
        ;
    }

    function it_saves_changes(UpdatePageInterface $updatePage)
    {
        $updatePage->saveChanges()->shouldBeCalled();

        $this->iSaveMyChanges();
    }

    function it_checks_if_a_resource_was_successfully_updated(NotificationCheckerInterface $notificationValidator)
    {
        $notificationValidator->checkEditionNotification('tax_category')->shouldBeCalled();

        $this->iShouldBeNotifiedAboutSuccessfulEdition();
    }

    function it_asserts_if_a_resource_was_successfully_updated(
        IndexPageInterface $indexPage,
        TaxCategoryInterface $taxCategory
    ) {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['code' => 'food_and_beverage', 'name' => 'Food and Beverage'])->willReturn(true);

        $taxCategory->getCode()->willReturn('food_and_beverage');

        $this->thisTaxCategoryNameShouldBe($taxCategory, 'Food and Beverage');
    }

    function resource_has_incorrectly_filled_fields_after_edition(
        IndexPageInterface $indexPage,
        TaxCategoryInterface $taxCategory
    ) {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['code' => 'food_and_beverage', 'name' => 'Food and Beverage'])->willReturn(false);

        $taxCategory->getCode()->willReturn('food_and_beverage');

        $this
            ->shouldThrow(new \InvalidArgumentException('Tax category name Food and Beverage was not assigned properly.'))
            ->during('thisTaxCategoryNameShouldBe', [$taxCategory, 'Food and Beverage'])
        ;
    }

    function it_checks_if_a_resource_was_not_created_because_of_unique_code_violation(CreatePageInterface $createPage)
    {
        $createPage->checkValidationMessageFor('code', 'The tax category with given code already exists.')->willReturn(true);

        $this->iShouldBeNotifiedThatTaxCategoryWithThisCodeAlreadyExists();
    }

    function it_throws_an_exception_if_the_message_on_a_page_is_not_related_to_unique_code_validation(
        CreatePageInterface $createPage
    ) {
        $createPage->checkValidationMessageFor('code', 'The tax category with given code already exists.')->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Unique code violation message should appear on page, but it does not.'))
            ->during('iShouldBeNotifiedThatTaxCategoryWithThisCodeAlreadyExists', [])
        ;
    }

    function it_asserts_that_only_one_resource_with_given_code_exist(IndexPageInterface $indexPage)
    {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(true);

        $this->thereShouldStillBeOnlyOneTaxCategoryWith('code', 'alcohol');
    }

    function it_throws_an_exception_if_not_only_one_resource_with_given_code_exist(IndexPageInterface $indexPage)
    {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Tax category with code alcohol cannot be founded.'))
            ->during('thereShouldStillBeOnlyOneTaxCategoryWith', ['code', 'alcohol'])
        ;
    }

    function it_checks_if_a_resource_was_not_created_because_of_required_code_violation(CreatePageInterface $createPage)
    {
        $createPage->checkValidationMessageFor('code', 'Please enter tax category code.')->willReturn(true);

        $this->iShouldBeNotifiedThatIsRequired('code');
    }

    function it_throws_an_exception_if_the_message_on_a_page_is_not_related_to_required_code_validation(
        CreatePageInterface $createPage
    ) {
        $createPage->checkValidationMessageFor('code', 'Please enter tax category code.')->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Tax category code should be required.'))
            ->during('iShouldBeNotifiedThatIsRequired', ['code'])
        ;
    }

    function it_checks_that_any_resource_with_given_name_exist(IndexPageInterface $indexPage)
    {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['name' => 'Food and Beverage'])->willReturn(false);

        $this->taxCategoryWithElementValueShouldNotBeAdded('name', 'Food and Beverage');
    }

    function it_throws_an_exception_if_resource_with_given_name_exist_but_it_should_not(IndexPageInterface $indexPage)
    {
        $indexPage->open()->shouldBeCalled();
        $indexPage->isResourceOnPage(['name' => 'Food and Beverage'])->willReturn(true);

        $this
            ->shouldThrow(new \InvalidArgumentException('Tax category with name Food and Beverage was created, but it should not.'))
            ->during('taxCategoryWithElementValueShouldNotBeAdded', ['name', 'Food and Beverage'])
        ;
    }
}
