<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContainerType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', ['read_only' => true])
            ->add('type', 'text', ['read_only' => true])
            ->add('translations', 'sylius_translations', [
                'type' => 'sylius_metadata_container_translation',
                'options' => [
                    'metadata_form' => $options['metadata_form'],
                ],
                'label' => 'sylius.form.translations',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('metadata_form');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_metadata_container';
    }
}
