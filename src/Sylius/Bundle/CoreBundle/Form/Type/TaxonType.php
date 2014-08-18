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

use Symfony\Component\Form\FormBuilderInterface;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType as BaseTaxonType;

/**
 * Taxon form type.
 */
class TaxonType extends BaseTaxonType
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
                'label' => 'sylius.form.taxon.file'
            )
        );
    }
}
