<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Api;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxon form type.
 */
class TaxonType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.taxon.name'
            ))
            ->add('permalink', 'text', array(
                'required' => false,
                'label' => 'sylius.form.taxon.permalink'
            ))
            ->add('description', 'text', array(
                'required' => false,
                'label' => 'sylius.form.taxon.description'
            ))
            ->add('parent', 'entity', array(
                'required' => false,
                'label' => 'sylius.form.taxon.parent',
                'class' => 'Sylius\Component\Core\Model\Taxon'
            ))
            ->add(
                'file',
                'file',
                array(
                    'label' => 'sylius.form.taxon.file'
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_api_taxon';
    }
}
