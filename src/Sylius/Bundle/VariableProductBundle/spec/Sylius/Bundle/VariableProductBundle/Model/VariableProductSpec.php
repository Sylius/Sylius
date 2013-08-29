<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariableProductBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariableProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Model\VariableProduct');
    }

    function it_is_a_Sylius_customizable_product()
    {
        $this->shouldImplement('Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface');
    }

    function it_should_not_have_master_variant_by_default()
    {
        $this->getMasterVariant()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\VariantInterface $variant
     */
    function its_master_variant_should_be_mutable_and_define_given_variant_as_master($variant)
    {
        $variant->setProduct($this)->shouldBeCalled();
        $variant->setMaster(true)->shouldBeCalled();

        $this->setMasterVariant($variant);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\VariantInterface $variant
     */
    function it_should_not_add_master_variant_twice_to_collection($variant)
    {
        $variant->isMaster()->willReturn(true);

        $variant->setProduct($this)->shouldBeCalled();
        $variant->setMaster(true)->shouldBeCalled();

        $this->setMasterVariant($variant);
        $this->setMasterVariant($variant);

        $this->hasVariants()->shouldReturn(false);
    }

    function its_hasVariants_should_return_false_if_no_variants_defined()
    {
        $this->hasVariants()->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\VariantInterface $variant
     */
    function its_hasVariants_should_return_true_only_if_any_variants_defined($variant)
    {
        $variant->isMaster()->willReturn(false);

        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($variant);
        $this->hasVariants()->shouldReturn(true);
    }

    function it_should_initialize_variants_collection_by_default()
    {
        $this->getVariants()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_should_initialize_option_collection_by_default()
    {
        $this->getOptions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_hasOptions_should_return_false_if_no_options_defined()
    {
        $this->hasOptions()->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function its_hasOptions_should_return_true_only_if_any_options_defined($option)
    {
        $this->addOption($option);
        $this->hasOptions()->shouldReturn(true);
    }

    /**
     * @param Doctrine\Common\Collections\Collection $options
     */
    function its_options_collection_should_be_mutable($options)
    {
        $this->setOptions($options);
        $this->getOptions()->shouldReturn($options);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function it_should_add_option_properly($option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function it_should_remove_option_properly($option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);

        $this->removeOption($option);
        $this->hasOption($option)->shouldReturn(false);
    }

}
