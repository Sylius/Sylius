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
use Prophecy\Argument;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\Taxonomy;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
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
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT  => 'postSubmit'
        ));
    }

    function it_adds_a_parent_form(
        $factory,
        FormEvent $event,
        FormInterface $form,
        TaxonInterface $taxon,
        TaxonInterface $parent,
        FormInterface $parentForm,
        TaxonomyInterface $taxonomy
    ) {
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $event->getData()->shouldBeCalled()->willReturn($taxon);

        $taxon->getId()->shouldBeCalled()->willReturn(null);
        $taxon->getTaxonomy()->shouldBeCalled()->willReturn($taxonomy);
        $taxon->getParent()->shouldBeCalled()->willReturn($parent);

        $factory->createNamed('parent', 'sylius_taxon_choice', $parent, array(
            'taxonomy'        => $taxonomy,
            'filter'          => null,
            'required'        => false,
            'label'           => 'sylius.form.taxon.parent',
            'empty_value'     => '---',
            'auto_initialize' => false,
        ))->shouldBeCalled()->willReturn($parentForm);

        $form->add($parentForm)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_sets_parent_to_the_taxon(
        FormEvent $event,
        TaxonInterface $taxon,
        TaxonomyInterface $taxonomy,
        TaxonInterface $root
    ) {
        $event->getData()->shouldBeCalled()->willReturn($taxon);
        $taxon->getTaxonomy()->shouldBeCalled()->willReturn($taxonomy);
        $taxon->getParent()->shouldBeCalled()->willReturn(null);
        $taxonomy->getRoot()->shouldBeCalled()->willReturn($root);

        $taxon->setParent($root)->shouldBeCalled();

        $this->postSubmit($event);
    }
}
