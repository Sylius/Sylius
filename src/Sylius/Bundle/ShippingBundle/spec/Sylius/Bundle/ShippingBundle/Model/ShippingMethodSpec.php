<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethodSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\ShippingMethod');
    }

    function it_implements_Sylius_shipping_method_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
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

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category
     */
    function it_allows_assigning_itself_to_category($category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category
     */
    function it_allows_detaching_itself_from_category($category)
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
        $this->getConfiguration()->shouldReturn(array());
    }

    function its_configuration_is_mutable()
    {
        $this->setConfiguration(array('charge' => 5));
        $this->getConfiguration()->shouldReturn(array('charge' => 5));
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
}
