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
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\TableManipulatorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'message' => '.message',
        'messageContent' => '.message > .content',
        'table' => '.table',
    ];

    /**
     * @var TableManipulatorInterface
     */
    private $tableManipulator;

    /**
     * @var string
     */
    private $resourceName;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableManipulatorInterface $tableManipulator
     * @param string $resourceName
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableManipulatorInterface $tableManipulator,
        $resourceName
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableManipulator = $tableManipulator;
        $this->resourceName = strtolower($resourceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSuccessMessage()
    {
        try {
            return $this->getElement('message')->hasClass('positive');
        } catch (ElementNotFoundException $exception) {
            return false;
        }
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
    public function isResourceOnPage(array $parameters)
    {
        try {
            $rows = $this->tableManipulator->getRowsWithFields($this->getElement('table'), $parameters);

            return 1 === count($rows);
        } catch (\InvalidArgumentException $exception) {
            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessage($message)
    {
        try {
            return $message === $this->getElement('messageContent')->getText();
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return sprintf('sylius_admin_%s_index', $this->resourceName);
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * @return TableManipulatorInterface
     */
    protected function getTableManipulator()
    {
        return $this->tableManipulator;
    }
}
