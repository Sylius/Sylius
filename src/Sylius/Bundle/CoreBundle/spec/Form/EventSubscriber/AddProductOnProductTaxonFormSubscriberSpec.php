<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddProductOnProductTaxonFormSubscriber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class AddProductOnProductTaxonFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddProductOnProductTaxonFormSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_post_submit_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::POST_SUBMIT => 'postSubmit']);
    }

    function it_adds_product_to_product_taxons(
        ProductInterface $product,
        FormEvent $event,
        ProductTaxonInterface $firstProductTaxon,
        ProductTaxonInterface $secondProductTaxon
    ) {
        $event->getData()->willReturn($product);

        $product
            ->getProductTaxons()
            ->willReturn(new ArrayCollection([$firstProductTaxon->getWrappedObject(), $secondProductTaxon->getWrappedObject()]))
        ;

        $firstProductTaxon->setProduct($product)->shouldBeCalled();
        $secondProductTaxon->setProduct($product)->shouldBeCalled();

        $this->postSubmit($event);
    }
}
