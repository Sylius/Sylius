<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Model\Variant');
    }

    public function it_implements_Sylius_variant_interface()
    {
        $this->shouldImplement('Sylius\Component\Variation\Model\VariantInterface');
    }

    public function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_should_not_belong_to_a_variable_by_default()
    {
        $this->getObject()->shouldReturn(null);
    }

    public function it_should_allow_assigning_itself_to_a_variable(VariableInterface $variable)
    {
        $this->setObject($variable);
        $this->getObject()->shouldReturn($variable);
    }

    public function it_should_allow_detaching_itself_from_a_variable(VariableInterface $variable)
    {
        $this->setObject($variable);
        $this->getObject()->shouldReturn($variable);

        $this->setObject(null);
        $this->getObject()->shouldReturn(null);
    }

    public function it_should_not_be_master_variant_by_default()
    {
        $this->shouldNotBeMaster();
    }

    public function it_is_master_variant_when_marked_so()
    {
        $this->shouldNotBeMaster();

        $this->setMaster(true);

        $this->shouldBeMaster();
    }

    public function it_should_not_have_presentation_by_default()
    {
        $this->getPresentation()->shouldReturn(null);
    }

    public function its_presentation_should_be_mutable()
    {
        $this->setPresentation('Super variant');
        $this->getPresentation()->shouldReturn('Super variant');
    }

    public function it_should_initialize_option_values_collection_by_default()
    {
        $this->getOptions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function its_option_values_collection_should_be_mutable(Collection $options)
    {
        $this->setOptions($options);
        $this->getOptions()->shouldReturn($options);
    }

    public function it_should_add_option_value_properly(OptionValueInterface $option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);
    }

    public function it_should_remove_option_value_properly(OptionValueInterface $option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);

        $this->removeOption($option);
        $this->hasOption($option)->shouldReturn(false);
    }

    public function it_throws_exception_if_trying_to_inherit_values_and_being_a_master_variant(VariantInterface $masterVariant)
    {
        $this->setMaster(true);

        $this
            ->shouldThrow('LogicException')
            ->duringSetDefaults($masterVariant)
        ;
    }

    public function it_throws_exception_if_trying_to_inherit_values_from_non_master_variant(VariantInterface $variant)
    {
        $variant->isMaster()->willReturn(false);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringSetDefaults($variant)
        ;
    }

    public function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function it_should_not_have_deletion_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    public function its_deletion_date_should_be_mutable()
    {
        $deletedAt = new \DateTime('now');

        $this->setDeletedAt($deletedAt);
        $this->getDeletedAt()->shouldReturn($deletedAt);
    }

    public function it_is_deleted_only_if_deletion_date_is_in_past()
    {
        $deletedAt = new \DateTime('yesterday');

        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();

        $deletedAt = new \DateTime('tomorrow');

        $this->setDeletedAt($deletedAt);
        $this->shouldNotBeDeleted();
    }

    public function it_should_not_be_deleted_by_default()
    {
        $this->shouldNotBeDeleted();
    }
}
