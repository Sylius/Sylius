<?php

namespace spec\Sylius\Bundle\ShippingBundle\Model;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Shipping method model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethod extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\ShippingMethod');
    }

    function it_should_be_a_Sylius_shipping_method()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_be_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_should_allow_disabling_itself()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_should_not_belong_to_category_by_default()
    {
        $this->getCategory()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category
     */
    function it_should_allow_assigning_itself_to_category($category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category
     */
    function it_should_allow_detaching_itself_from_category($category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);

        $this->setCategory(null);
        $this->getCategory()->shouldReturn(null);
    }

    function it_should_have_match_any_requirement_by_default()
    {
        $this->getRequirement()->shouldReturn(ShippingMethodInterface::REQUIREMENT_MATCH_ANY);
    }

    function its_matching_requirement_should_be_mutable()
    {
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_NONE);
        $this->getRequirement()->shouldReturn(ShippingMethodInterface::REQUIREMENT_MATCH_NONE);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_complain_if_disabled_and_trying_to_match_shipment($shipment)
    {
        $this->setEnabled(false);
        $this
            ->shouldThrow('LogicException')
            ->duringMatches($shipment)
        ;
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_match_any_shipment_if_there_is_no_category_defined($shipment)
    {
        $this->matches($shipment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface         $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable3
     */
    function it_should_match_shipment_if_none_of_shippables_has_same_category_when_requirement_says_so(
        $shipment, $category1, $category2, $shippable1, $shippable2, $shippable3
    )
    {
        $this->setCategory($category1);
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_NONE);

        $shippable1->getCategory()->willReturn($category2);
        $shippable2->getCategory()->willReturn($category2);
        $shippable3->getCategory()->willReturn($category2);

        $shipment->getShippables()->willReturn(array($shippable1, $shippable2, $shippable3));

        $this->matches($shipment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface         $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable3
     */
    function it_should_not_match_shipment_if_one_of_shippables_has_same_category_when_requirement_says_opposite(
        $shipment, $category1, $category2, $shippable1, $shippable2, $shippable3
    )
    {
        $this->setCategory($category1);
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_NONE);

        $shippable1->getCategory()->willReturn($category2);
        $shippable2->getCategory()->willReturn($category1);
        $shippable3->getCategory()->willReturn($category2);

        $shipment->getShippables()->willReturn(array($shippable1, $shippable2, $shippable3));

        $this->matches($shipment)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface         $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable3
     */
    function it_should_match_shipment_if_one_of_shippables_has_same_category_when_requirement_says_so(
        $shipment, $category1, $category2, $shippable1, $shippable2, $shippable3
    )
    {
        $this->setCategory($category1);
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_ANY);

        $shippable1->getCategory()->willReturn($category2);
        $shippable2->getCategory()->willReturn($category1);
        $shippable3->getCategory()->willReturn($category2);

        $shipment->getShippables()->willReturn(array($shippable1, $shippable2, $shippable3));

        $this->matches($shipment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface         $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable3
     */
    function it_should_not_match_shipment_if_none_of_shippables_has_same_category_when_requirement_says_opposite(
        $shipment, $category1, $category2, $shippable1, $shippable2, $shippable3
    )
    {
        $this->setCategory($category1);
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_ANY);

        $shippable1->getCategory()->willReturn($category2);
        $shippable2->getCategory()->willReturn($category2);
        $shippable3->getCategory()->willReturn($category2);

        $shipment->getShippables()->willReturn(array($shippable1, $shippable2, $shippable3));

        $this->matches($shipment)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface         $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable3
     */
    function it_should_match_shipment_if_all_of_shippables_has_same_category_when_requirement_says_so(
        $shipment, $category1, $category2, $shippable1, $shippable2, $shippable3
    )
    {
        $this->setCategory($category1);
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_ALL);

        $shippable1->getCategory()->willReturn($category1);
        $shippable2->getCategory()->willReturn($category1);
        $shippable3->getCategory()->willReturn($category1);

        $shipment->getShippables()->willReturn(array($shippable1, $shippable2, $shippable3));

        $this->matches($shipment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface         $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $category2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable1
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface        $shippable3
     */
    function it_should_not_match_shipment_if_one_of_shippables_has_different_category_when_requirement_says_opposite(
        $shipment, $category1, $category2, $shippable1, $shippable2, $shippable3
    )
    {
        $this->setCategory($category1);
        $this->setRequirement(ShippingMethodInterface::REQUIREMENT_MATCH_ALL);

        $shippable1->getCategory()->willReturn($category1);
        $shippable2->getCategory()->willReturn($category2);
        $shippable3->getCategory()->willReturn($category1);

        $shipment->getShippables()->willReturn(array($shippable1, $shippable2, $shippable3));

        $this->matches($shipment)->shouldReturn(false);
    }

    function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Shippingable goods');
        $this->getName()->shouldReturn('Shippingable goods');
    }

    function it_should_not_have_calculator_defined_by_default()
    {
        $this->getCalculator()->shouldReturn(null);
    }

    function its_calculator_should_be_mutable()
    {
        $this->setCalculator('default');
        $this->getCalculator()->shouldReturn('default');
    }

    function it_should_initialize_array_for_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn(array());
    }

    function its_configuration_should_be_mutable()
    {
        $this->setConfiguration(array('charge' => 5));
        $this->getConfiguration()->shouldReturn(array('charge' => 5));
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
