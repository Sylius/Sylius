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

use Sylius\Bundle\ProductBundle\Form\Type\ProductTranslationType as BaseProductTranslationType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductTranslationType extends BaseProductTranslationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('shortDescription', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.product.short_description',
            ])
        ;
    }
}
