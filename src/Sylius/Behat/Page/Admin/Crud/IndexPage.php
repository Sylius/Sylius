<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\TableManipulatorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var TableManipulatorInterface
     */
    protected $tableManipulator;

    /**
     * @var array
     */
    protected  $elements = [
        'message' => '.message',
        'messageContent' => '.message > .content',
        'table' => '.table',
    ];

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableManipulatorInterface $tableManipulator
     * @param string $resourceName
     */
    public function __construct(Session $session, array $parameters, RouterInterface $router, TableManipulatorInterface $tableManipulator, $resourceName)
    {
        parent::__construct($session, $parameters, $router);

        $this->tableManipulator = $tableManipulator;
        $this->resourceName = $resourceName;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfulMessage()
    {
        return $this->getElement('message')->hasClass('positive');
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyCreated()
    {
        return $this->hasMessage(sprintf('Success %s has been successfully created.', ucfirst($this->resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyUpdated()
    {
        return $this->hasMessage(sprintf('Success %s has been successfully updated.', ucfirst($this->resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyDeleted()
    {
        return $this->hasMessage(sprintf('Success %s has been successfully deleted.', ucfirst($this->resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isResourceAppearInTheStoreBy(array $parameters)
    {
        $rows = $this->tableManipulator->getRowsWithFields($this->getElement('table'), $parameters);

        if (!empty($rows)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessage($message)
    {
        if ($message === $this->getElement('messageContent')->getText()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return 'sylius_admin_' . strtolower($this->resourceName) . '_index';
    }
}
