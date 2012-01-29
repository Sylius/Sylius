<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

        foreach($this->statusManager->getStatuses() as $id => $status) {
            $choices[] = $this->statusManager->translateStatus($id);
        }

        $this->choices = $choices;

        return parent::getChoices();
    }
}
