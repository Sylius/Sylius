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

use Sylius\Bundle\SalesBundle\Form\ChoiceList\StatusChoiceList;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Order status type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusFormType extends AbstractType
{
    /**
     * Data class.
     * 
     * @var string
     */
    protected $dataClass;
    
    /**
     * Status choice list.
     * 
     * @var StatusChoiceList
     */
    protected $statusChoiceList;
    
    /**
     * Constructor.
     * 
     * @param string $dataClass
     */
    public function __construct($dataClass, StatusChoiceList $statusChoiceList)
    {
        $this->dataClass = $dataClass;
        $this->statusChoiceList = $statusChoiceList;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name');
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
    
    public function getName()
    {
        return 'sylius_sales_status';
    }
}
