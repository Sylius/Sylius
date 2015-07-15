<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ResizeZoneMemberCollectionListenerSpec extends ObjectBehavior
{
    function let(
        FormFactoryInterface $factory,
        FormInterface $form,
        FormConfigInterface $formConfig,
        ResolvedFormTypeInterface $type
    ) {
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getType()->willReturn($type);
        $formConfig->getDataClass()->willReturn('Class');
        $type->getName()->willReturn();

        $this->beConstructedWith($factory, array($form));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\EventListener\ResizeZoneMemberCollectionListener');
    }

    function it_is_resize_form_listener()
    {
        $this->shouldHaveType('Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener');
    }
}
