<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type\DataFetcher;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DataFetcherChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $dataFetchers = array(
            'user_registration' => 'User Registration',
        );
        $this->beConstructedWith($dataFetchers);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\DataFetcher\DataFetcherChoiceType');
    }

    public function it_extends_abstract_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_data_fetcher_choice');
    }

    public function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_defines_data_fetcher_choices(OptionsResolverInterface $resolver)
    {
        $dataFetchers = array(
            'user_registration' => 'User Registration',
        );

        $resolver->setDefaults(array('choices' => $dataFetchers))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
