<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Cart form form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartFormType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Constructor.
     *
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('items', 'collection', array(
            'type' => 'sylius_cart_item',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => $this->dataClass
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_carts_cart';
    }
}
