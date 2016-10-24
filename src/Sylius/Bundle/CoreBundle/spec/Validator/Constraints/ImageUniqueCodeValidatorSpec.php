<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraint;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ImageUniqueCode;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ImageUniqueCodeValidator;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ImageUniqueCodeValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ImageUniqueCodeValidator::class);
    }

    function it_extends_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation_if_there_two_images_with_the_same_owner_which_have_same_codes(
        ImageInterface $firstImage,
        ImageInterface $secondImage
    ) {
        $firstImage->getCode()->willReturn('car');
        $secondImage->getCode()->willReturn('car');

        $this->addViolationAt('[0].code', Argument::type('string'))->shouldBeCalled();
        $this->addViolationAt('[1].code', Argument::type('string'))->shouldBeCalled();

        $this->validate(
            new ArrayCollection($firstImage->getWrappedObject(), $secondImage->getWrappedObject()),
            new ImageUniqueCode()
        );
    }

    function it_does_not_add_violation_if_there_is_no_duplication_of_a_code(
        ImageInterface $firstImage,
        ImageInterface $secondImage
    ) {
        $firstImage->getCode()->willReturn('car');
        $secondImage->getCode()->willReturn('wipers');

        $this->addViolationAt('[0].code', Argument::type('string'))->shouldNotBeCalled();
        $this->addViolationAt('[1].code', Argument::type('string'))->shouldNotBeCalled();

        $this->validate(
            new ArrayCollection($firstImage->getWrappedObject(), $secondImage->getWrappedObject()),
            new ImageUniqueCode()
        );
    }
}
