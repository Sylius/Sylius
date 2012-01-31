<?php

namespace Sylius\Bundle\SalesBundle\Manipulator;

use Sylius\Bundle\SalesBundle\Model\StatusManagerInterface;

use Sylius\Bundle\SalesBundle\Model\StatusInterface;

/**
 * Order manipulator.
 * 
 * @author Pawel Jedrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusManipulator implements StatusManipulatorInterface
{
    protected $statusManager;
    
    public function __construct(StatusManagerInterface $statusManager)
    {
        $this->StatusManager = $statusManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(StatusInterface $status)
    {
        $status->incrementCreatedAt();
        $this->StatusManager->persistStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function update(StatusInterface $status)
    {
        $status->incrementUpdatedAt();
        $this->StatusManager->persistStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(StatusInterface $status)
    {
        $this->StatusManager->removeStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function open(StatusInterface $status)
    {
        $status->setClosed(false);
        $this->update($status);
    }
}
