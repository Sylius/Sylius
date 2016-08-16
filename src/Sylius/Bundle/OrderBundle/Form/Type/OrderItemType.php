<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItemType extends AbstractResourceType
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
                'label' => 'sylius.form.order_item.quantity',
                'constraints' => [
                    new NotNull(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('unitPrice', 'sylius_money', [
                'label' => 'sylius.form.order_item.unit_price',
                'constraints' => [
                    new NotNull(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->setDataMapper($this->orderItemQuantityDataMapper)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_order_item';
    }
}
