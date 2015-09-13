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

use Sylius\Bundle\ProductBundle\Form\Type\ProductReviewAdminType as BaseProductReviewAdminType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class ProductReviewAdminType extends BaseProductReviewAdminType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->get('author')->resetModelTransformers();

        $builder
            ->remove('author')
            ->add('author', 'entity', array(
                'class'    => 'Sylius\Component\Core\Model\Customer',
                'label'    => 'sylius.form.review.author',
                'property' => 'email',
            ))
        ;
    }
}
