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

use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType as BaseOrderItemType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Order item form type for api creation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItemType extends BaseOrderItemType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('variant', 'entity_hidden', array(
                'data_class' => 'Sylius\Component\Core\Model\ProductVariant'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_api_order_item';
    }
}
