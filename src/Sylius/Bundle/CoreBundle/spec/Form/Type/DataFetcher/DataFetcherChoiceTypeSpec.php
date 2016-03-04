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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DataFetcherChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $dataFetchers = [
            'sylius_data_fetcher_user_registration' => 'User Registration',
        ];
        $this->beConstructedWith($dataFetchers);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\DataFetcher\DataFetcherChoiceType');
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_data_fetcher_choice');
    }

    function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_defines_data_fetcher_choices(OptionsResolver $resolver)
    {
        $dataFetchers = [
            'sylius_data_fetcher_user_registration' => 'User Registration',
        ];

        $resolver->setDefaults(['choices' => $dataFetchers])->shouldBeCalled();

        $this->configureOptions($resolver);
    }
}
