<?php

namespace Sylius\Bundle\SalesBundle\Form\ChoiceList;

use Sylius\Bundle\SalesBundle\Model\StatusManager;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class StatusChoiceList extends ChoiceList
{
    /**
     * @var StatusManager
     */
    protected $statusManager;

    /**
     * Constructor.
     *
     * @param $pointManager
     */
    public function __construct(StatusManager $statusManager)
    {
        $this->statusManager = $statusManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = array();

        $this->choices = $this->statusManager->findStatuses();

        return parent::getChoices();
    }
}
