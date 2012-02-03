<?php

namespace Sylius\Bundle\SalesBundle\Form\Type;

use Sylius\Bundle\SalesBundle\Form\ChoiceList\StatusChoiceList;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class StatusListFormType extends AbstractType
{
    /**
     * The class that holds the data.
     *
     * @var dataClass
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
    public function __construct($class, StatusChoiceList $statusChoiceList)
    {
        $this->dataClass = $class;
        $this->statusChoiceList = $statusChoiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('status', 'entity', array(
                'property' => 'name',
                'class' => 'SyliusSalesBundle:Status',
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

    public function getName()
    {
        return 'sylius_sales_status_list';
    }
}
