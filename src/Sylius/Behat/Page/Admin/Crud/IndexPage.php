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
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
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
        'table' => '.table',
    ];

    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @var string
     */
    private $resourceName;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableAccessorInterface $tableAccessor
     * @param string $resourceName
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        $resourceName
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
        $this->resourceName = strtolower($resourceName);
    }

    /**
     * {@inheritdoc}
     */
    public function isResourceOnPage(array $parameters)
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields($this->getElement('table'), $parameters);

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
    public function deleteResourceOnPage(array $parameters)
    {
        $tableManipulator = $this->getTableManipulator();
        $table = $this->getElement('table');

        $deletedRow = $tableManipulator->getRowWithFields($table, $parameters);
        $actionButtons = $tableManipulator->getFieldFromRow($table, $deletedRow, 'Actions');

        $actionButtons->pressButton('Delete');
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
     * @return TableAccessorInterface
     */
    protected function getTableAccessor()
    {
        return $this->tableAccessor;
    }
}
