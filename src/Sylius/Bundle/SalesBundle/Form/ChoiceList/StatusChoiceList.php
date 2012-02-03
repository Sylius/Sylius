<?php

namespace Sylius\Bundle\SalesBundle\Form\ChoiceList;

use Sylius\Bundle\SalesBundle\Model\StatusManager;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

/**
 * Order status choice list.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusChoiceList extends ChoiceList
{
    protected $choices;

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
        $this->choices = $this->statusManager->findStatuses();

/* -- commenting out for now incase translation wants to happen again
        $choices = array();

        foreach($this->statusManager->getStatuses() as $id => $status) {
            $choices[] = $this->statusManager->translateStatus($id);
        }

        $this->choices = $choices;

*/
        return parent::getChoices();
    }
}
