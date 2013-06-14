<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyType as BaseTaxonomyType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxonomy form type.
 */
class TaxonomyType extends BaseTaxonomyType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'file',
            'file',
            array(
                'property_path' => 'root.file',
                'label' => 'sylius.form.taxonomy.file'
            )
        );
    }
}
