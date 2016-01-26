<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomyBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class BuildTaxonFormSubscriberSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\EventListener\BuildTaxonFormSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
        ]);
    }

    function it_adds_a_parent_form(
        $factory,
        FormEvent $event,
        FormInterface $form,
        TaxonInterface $taxon,
        TaxonInterface $parent,
        FormInterface $parentForm
    ) {
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $event->getData()->shouldBeCalled()->willReturn($taxon);

        $taxon->getId()->shouldBeCalled()->willReturn(null);
        $taxon->getParent()->shouldBeCalled()->willReturn($parent);

        $factory->createNamed('parent', 'sylius_taxon_choice', $parent, [
            'filter' => null,
            'required' => false,
            'label' => 'sylius.form.taxon.parent',
            'empty_value' => '---',
            'auto_initialize' => false,
        ])->shouldBeCalled()->willReturn($parentForm);

        $form->add($parentForm)->shouldBeCalled();

        $this->preSetData($event);
    }
}
