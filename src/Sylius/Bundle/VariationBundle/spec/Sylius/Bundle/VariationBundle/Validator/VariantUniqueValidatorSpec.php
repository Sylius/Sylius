<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Validator;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\VariationBundle\Validator\Constraint\VariantUnique;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantUniqueValidatorSpec extends ObjectBehavior
{
    function let(
        ObjectRepository $variantRepository,
        ExecutionContextInterface $context
    )
    {
        $this->beConstructedWith($variantRepository);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Validator\VariantUniqueValidator');
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldImplement('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_should_add_violation_if_variant_with_given_property_value_already_exists(
        $variantRepository,
        VariantInterface $variant,
        VariantInterface $conflictualVariant,
        $context
    )
    {
        $constraint = new VariantUnique(array(
            'property' => 'presentation',
            'message'  => 'Variant with given presentation already exists'
        ));

        $variant->getPresentation()->willReturn('IPHONE5WHITE');
        $variantRepository->findOneBy(array('presentation' => 'IPHONE5WHITE'))->shouldBeCalled()->willReturn($conflictualVariant);

        $context->addViolationAt('presentation', 'Variant with given presentation already exists', Argument::any())->shouldBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_should_not_add_violation_if_variant_with_given_property_value_does_not_exist(
        $variantRepository,
        VariantInterface $variant,
        $context
    )
    {
        $constraint = new VariantUnique(array(
            'property' => 'presentation',
            'message'  => 'Variant with given presentation already exists'
        ));

        $variant->getPresentation()->willReturn('111AAA');
        $variantRepository->findOneBy(array('presentation' => '111AAA'))->shouldBeCalled()->willReturn(null);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_should_not_add_violation_if_conflictual_variant_and_validated_one_are_the_same(
        $variantRepository,
        VariantInterface $variant,
        $context
    )
    {
        $constraint = new VariantUnique(array(
            'property' => 'presentation',
            'message'  => 'Variant with given presentation already exists'
        ));

        $variant->getPresentation()->willReturn('111AAA');
        $variantRepository->findOneBy(array('presentation' => '111AAA'))->shouldBeCalled()->willReturn($variant);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }
}
