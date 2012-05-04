<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Order form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderType extends AbstractType
{
    /**
     * Modes.
     */
    const MODE_PLACE         = 0;
    const MODE_CREATE        = 1;
    const MODE_UPDATE        = 2;
    const MODE_CHANGE_STATUS = 3;

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
        switch ($options['mode']) {
            case self::MODE_CREATE:
            case self::MODE_UPDATE:
                $builder->add('items', 'collection', array(
                    'type'         => 'sylius_sales_item',
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ));
            break;

            case self::MODE_CHANGE_STATUS:
                $builder->add('status', 'sylius_sales_status_choice', array(
                    'label' => 'sylius_sales.label.order.status'
                ));
            break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'data_class' => $this->dataClass,
            'mode'       => self::MODE_PLACE
        );
    }

    public function getName()
    {
        return 'sylius_sales_order';
    }
}
