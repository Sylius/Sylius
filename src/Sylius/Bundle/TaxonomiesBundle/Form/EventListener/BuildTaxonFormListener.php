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

use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
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
            FormEvents::POST_SUBMIT  => 'postBind'
        );
    }

    /**
     * Builds proper taxon form after setting the product.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $taxon = $event->getData();

        if (null === $taxon) {
            return;
        }

        $taxonomy = $taxon->getTaxonomy();

        $event->getForm()->add($this->factory->createNamed('parent', 'sylius_taxon_choice', $taxon->getParent(), array(
            'taxonomy'    => $taxonomy,
            'filter'      => $this->getFilterTaxonOption($taxon),
            'required'    => false,
            'label'       => 'sylius.form.taxon.parent',
            'empty_value' => '---',
            'auto_initialize' => false,
        )));
    }

    /**
     * Reset the taxon root if it's null.
     *
     * @param FormEvent $event
     */
    public function postBind(FormEvent $event)
    {
        $taxon = $event->getData();

        $taxonomy = $taxon->getTaxonomy();

        if (null === $taxon->getParent()) {
            $taxon->setParent($taxonomy->getRoot());
        }
    }

    /**
     * Get the closure to filter taxon collection
     *
     * @param  TaxonInterface $taxon
     * @return callable|null
     */
    private function getFilterTaxonOption(TaxonInterface $taxon)
    {
        $closure = null;

        if ($taxon->getId()) {
            $closure = function ($entry) use ($taxon) {
                return $entry->getId() != $taxon->getId();
            };
        }

        return $closure;
    }
}
