<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\ResourceTranslationsSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin ResourceTranslationsType
 *
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ResourceTranslationsTypeSpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceTranslationsType::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_translations');
    }

    function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn('collection');
    }

    function it_builds_form(LocaleProviderInterface $localeProvider, FormBuilderInterface $builder)
    {
        $localeProvider->getDefinedLocalesCodes()->willReturn(['pl_PL', 'en_EN', 'en_GB']);
        $localeProvider->getDefaultLocaleCode()->willReturn('en_EN');

        $builder
            ->addEventSubscriber(Argument::type(ResourceTranslationsSubscriber::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, [
            'type' => 'text'
        ]);
    }
}

