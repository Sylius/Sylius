<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\TaxonomyBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class TaxonType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => TaxonTranslationType::class,
                'label' => 'sylius.form.taxon.name',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                if (null === $event->getData()) {
                    return;
                }

                $event->getForm()->add('parent', TaxonAutocompleteChoiceType::class, [
                    'label' => 'sylius.form.taxon.parent',
                    'required' => false,
                ]);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_taxon';
    }
}
