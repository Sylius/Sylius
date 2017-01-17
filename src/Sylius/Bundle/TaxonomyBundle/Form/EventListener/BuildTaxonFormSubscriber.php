<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Form\EventListener;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @internal
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class BuildTaxonFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
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
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $taxon = $event->getData();

        if (null === $taxon) {
            return;
        }

        $event->getForm()->add($this->factory->createNamed('parent', TaxonChoiceType::class, $taxon->getParent(), [
            'filter' => $this->getFilterTaxonOption($taxon),
            'required' => false,
            'label' => 'sylius.form.taxon.parent',
            'placeholder' => '---',
            'auto_initialize' => false,
        ]));
    }

    /**
     * @param TaxonInterface $taxon
     *
     * @return callable|null
     */
    private function getFilterTaxonOption(TaxonInterface $taxon)
    {
        if (null === $taxon->getId()) {
            return null;
        }

        return function (TaxonInterface $entry) use ($taxon) {
            return $entry->getId() !== $taxon->getId();
        };
    }
}
