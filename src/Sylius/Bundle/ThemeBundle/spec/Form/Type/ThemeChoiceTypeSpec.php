<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @author Kasra Khosravi <kasrakhosravi2@gmail.com>
 */
class ThemeChoiceTypeSpec extends ObjectBehavior
{
    function let(ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($themeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Form\Type\ThemeChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_options(
        OptionsResolver $resolver,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ) {
        $resolver->setDefault('choice_list', Argument::type(ObjectChoiceList::class))->shouldBeCalled()->willReturn($resolver);
        $themeRepository->findAll()->shouldBeCalled()->willReturn([$theme]);

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_theme_choice');
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn(ChoiceType::class);
    }
}
