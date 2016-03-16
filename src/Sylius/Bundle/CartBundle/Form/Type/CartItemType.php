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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartItemType extends AbstractResourceType
{
    /**
     * @var DataMapperInterface
     */
    protected $orderItemQuantityDataMapper;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param DataMapperInterface $orderItemQuantityDataMapper
     */
    public function __construct($dataClass, array $validationGroups = [], DataMapperInterface $orderItemQuantityDataMapper)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->orderItemQuantityDataMapper = $orderItemQuantityDataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer', [
                'attr' => ['min' => 1],
                'label' => 'sylius.form.cart_item.quantity',
            ])
            ->setDataMapper($this->orderItemQuantityDataMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_cart_item';
    }
}
