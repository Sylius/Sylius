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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\VariationBundle\Validator\Constraint\VariantUnique;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantUniqueValidatorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $variantRepository,
        ExecutionContextInterface $context
    ) {
        $this->beConstructedWith($variantRepository);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Validator\VariantUniqueValidator');
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidator::class);
    }

    function it_should_add_violation_if_variant_with_given_property_value_already_exists(
        $variantRepository,
        VariantInterface $variant,
        VariantInterface $conflictualVariant,
        $context
    ) {
        $constraint = new VariantUnique([
            'property' => 'presentation',
            'message' => 'Variant with given presentation already exists',
        ]);

        $variant->getPresentation()->willReturn('IPHONE5WHITE');
        $variantRepository->findOneBy(['presentation' => 'IPHONE5WHITE'])->willReturn($conflictualVariant);

        $context->addViolationAt('presentation', 'Variant with given presentation already exists', Argument::any())->shouldBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_should_not_add_violation_if_variant_with_given_property_value_does_not_exist(
        $variantRepository,
        VariantInterface $variant,
        $context
    ) {
        $constraint = new VariantUnique([
            'property' => 'presentation',
            'message' => 'Variant with given presentation already exists',
        ]);

        $variant->getPresentation()->willReturn('111AAA');
        $variantRepository->findOneBy(['presentation' => '111AAA'])->willReturn(null);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_should_not_add_violation_if_conflictual_variant_and_validated_one_are_the_same(
        $variantRepository,
        VariantInterface $variant,
        $context
    ) {
        $constraint = new VariantUnique([
            'property' => 'presentation',
            'message' => 'Variant with given presentation already exists',
        ]);

        $variant->getPresentation()->willReturn('111AAA');
        $variantRepository->findOneBy(['presentation' => '111AAA'])->willReturn($variant);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }
}
