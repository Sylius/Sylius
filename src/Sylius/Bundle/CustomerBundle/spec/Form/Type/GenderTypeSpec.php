<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CustomerBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GenderTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CustomerBundle\Form\Type\GenderType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                CustomerInterface::UNKNOWN_GENDER => 'sylius.gender.unknown',
                CustomerInterface::MALE_GENDER => 'sylius.gender.male',
                CustomerInterface::FEMALE_GENDER => 'sylius.gender.female',
            ],
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_gender');
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
