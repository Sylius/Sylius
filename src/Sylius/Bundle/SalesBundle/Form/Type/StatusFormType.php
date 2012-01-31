<?php

namespace Sylius\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class StatusFormType extends AbstractType
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
        $builder->add('name');
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