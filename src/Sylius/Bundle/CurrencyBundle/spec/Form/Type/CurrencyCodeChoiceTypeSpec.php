<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyCodeChoiceType;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class CurrencyCodeChoiceTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyCodeChoiceType::class);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_define_assigned_data_class_and_validation_groups(
        RepositoryInterface $currencyRepository,
        OptionsResolver $resolver,
        Currency $currency
    ) {
        $currencyRepository->findBy(['enabled' => true])->willReturn([$currency]);
        $currency->getCode()->willReturn('EUR');
        $currency->getName()->willReturn('Euro');

        $resolver
            ->setDefaults([
                'choice_translation_domain' => false,
                'choices' => ['EUR' => 'EUR - Euro'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_currency_code_choice');
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
