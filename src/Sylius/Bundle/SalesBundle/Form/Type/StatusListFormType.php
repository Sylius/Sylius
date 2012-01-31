<?php

namespace Sylius\Bundle\SalesBundle\Form\Type;

use Sylius\Bundle\SalesBundle\Form\ChoiceList\StatusChoiceList;
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
            ->add('status', 'choice', array(
                'choice_list' => $this->statusChoiceList,
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
