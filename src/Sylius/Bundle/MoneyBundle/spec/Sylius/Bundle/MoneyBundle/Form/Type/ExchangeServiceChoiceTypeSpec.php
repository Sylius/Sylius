<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ExchangeServiceChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('ExchangeRateConfig');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Form\Type\ExchangeServiceChoiceType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_should_define_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('class' => 'ExchangeRateConfig'))->shouldBeCalled();
        $this->setDefaultOptions($resolver);

    }
}
