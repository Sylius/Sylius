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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Status choice form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusChoiceType extends AbstractType
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
    public function getDefaultOptions()
    {
        return array(
            'data_class'  => $this->dataClass,
            'choice_list' => $this->statusChoiceList
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_sales_status_choice';
    }
}
