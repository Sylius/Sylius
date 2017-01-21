<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class ShippingMethodSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShippingMethod::class);
    }

    function it_implements_Sylius_shipping_method_interface()
    {
        $this->shouldImplement(ShippingMethodInterface::class);
    }

    function it_implements_Sylius_toogleable_interface()
    {
        $this->shouldImplement('Sylius\Component\Resource\Model\ToggleableInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('SC2');
        $this->getCode()->shouldReturn('SC2');
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_allows_disabling_itself()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_does_not_belong_to_category_by_default()
    {
        $this->getCategory()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_category(ShippingCategoryInterface $category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    function it_allows_detaching_itself_from_category(ShippingCategoryInterface $category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);

        $this->setCategory(null);
        $this->getCategory()->shouldReturn(null);
    }

    function it_has_match_any_category_requirement_by_default()
    {
        $this->getCategoryRequirement()->shouldReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);
    }

    function its_category_matching_requirement_is_mutable()
    {
        $this->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE);
        $this->getCategoryRequirement()->shouldReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE);
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Shippable goods');
        $this->getName()->shouldReturn('Shippable goods');
    }

    function its_description_is_mutable()
    {
        $this->setDescription('Very good shipping, cheap price, good delivery time.');
        $this->getDescription()->shouldReturn('Very good shipping, cheap price, good delivery time.');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('Shippable goods');
        $this->__toString()->shouldReturn('Shippable goods');
    }

    function it_has_no_calculator_defined_by_default()
    {
        $this->getCalculator()->shouldReturn(null);
    }

    function its_calculator_is_mutable()
    {
        $this->setCalculator('default');
        $this->getCalculator()->shouldReturn('default');
    }

    function it_initializes_array_for_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    function its_configuration_is_mutable()
    {
        $this->setConfiguration(['charge' => 5]);
        $this->getConfiguration()->shouldReturn(['charge' => 5]);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_no_archiving_date_by_default()
    {
        $this->getArchivedAt()->shouldReturn(null);
    }

    function its_archiving_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setArchivedAt($date);
        $this->getArchivedAt()->shouldReturn($date);
    }
}
