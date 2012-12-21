<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Adds the parent taxon field choice based on the selected taxonomy.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BuildTaxonFormListener implements EventSubscriberInterface
{
    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_BIND => 'postBind'
        );
    }

    /**
     * Builds proper taxon form after setting the product.
     *
     * @param DataEvent $event
     */
    public function preSetData(DataEvent $event)
    {
        $taxon = $event->getData();
        $form = $event->getForm();

        if (null === $taxon) {
            return;
        }

        $taxonomy = $taxon->getTaxonomy();

        $form->add($this->factory->createNamed('parent', 'sylius_taxonomies_taxon_choice', $taxon->getParent(), array(
            'taxonomy' => $taxonomy,
            'required' => false
        )));
    }

    /**
     * Reset the taxon root if it's null.
     *
     * @param DataEvent $event
     */
    public function postBind(DataEvent $event)
    {
        $taxon = $event->getData();
        $form = $event->getForm();

        $taxonomy = $taxon->getTaxonomy();

        if (null === $taxon->getParent()) {
            $taxon->setParent($taxonomy->getRoot());
        }
    }
}
