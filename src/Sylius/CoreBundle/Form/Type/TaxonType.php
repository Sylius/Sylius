<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Form\Type;

use Sylius\TaxonomyBundle\Form\Type\TaxonType as BaseTaxonType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxonType extends BaseTaxonType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('file', 'file', ['label' => 'sylius.form.taxon.file']);
    }
}
