<?php

namespace spec\Sylius\Bundle\TaxonomyBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Symfony\Component\Form\FormEvent;

class BuildTaxonomyFormListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\EventListener\BuildTaxonomyFormListener');
    }

    function it_should_accept_a_post_submit_form_event(FormEvent $formEvent)
    {
        $this->postSubmit($formEvent);
    }

    function it_should_set_the_taxonomy_name(FormEvent $formEvent, TaxonomyInterface $taxonomy)
    {
        $taxonomyName = 'Taxonomy';
        $formEvent->getData()->willReturn($taxonomy);
        $taxonomy->getName()->willReturn($taxonomyName);
        $taxonomy->setName($taxonomyName)->shouldBeCalled();

        $this->postSubmit($formEvent);
    }
}
