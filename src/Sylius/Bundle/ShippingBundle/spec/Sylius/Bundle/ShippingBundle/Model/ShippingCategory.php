<?php

namespace spec\Sylius\Bundle\ShippingBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Shipping category model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingCategory extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\ShippingCategory');
    }

    function it_should_be_a_Sylius_shipping_category()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
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

    function it_should_not_have_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_should_be_mutable()
    {
        $this->setDescription('All shippingable goods');
        $this->getDescription()->shouldReturn('All shippingable goods');
    }

    function it_should_initialize_methods_collection_by_default()
    {
        $this->getMethods()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $shippingMethod
     */
    function it_should_add_methods_properly($shippingMethod)
    {
        $this->hasMethod($shippingMethod)->shouldReturn(false);

        $shippingMethod->setCategory($this)->shouldBeCalled();
        $this->addMethod($shippingMethod);

        $this->hasMethod($shippingMethod)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $shippingMethod
     */
    function it_should_remove_methods_properly($shippingMethod)
    {
        $this->hasMethod($shippingMethod)->shouldReturn(false);

        $shippingMethod->setCategory($this)->shouldBeCalled();
        $this->addMethod($shippingMethod);

        $shippingMethod->setCategory(null)->shouldBeCalled();
        $this->removeMethod($shippingMethod);

        $this->hasMethod($shippingMethod)->shouldReturn(false);
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
