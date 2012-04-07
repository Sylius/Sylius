<?php

namespace Sylius\Bundle\SalesBundle\Form\ChoiceList;

use Sylius\Bundle\SalesBundle\Model\StatusManagerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

/**
 * Order status choice list.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusChoiceList extends ObjectChoiceList
{
    /**
     * @var StatusManagerInterface
     */
    protected $statusManager;

    /**
     * Constructor.
     *
     * @param StatusManagerInterface $statusManager
     */
    public function __construct(StatusManagerInterface $statusManager)
    {
        $this->statusManager = $statusManager;

        parent::__construct($statusManager->findStatuses(), 'name', array(), null, null, 'id');
    }
}
