<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Channel;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\TableManipulatorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ChannelIndexPage extends SymfonyPage
{
    /**
     * @var TableManipulatorInterface
     */
    private $tableManipulator;

    /**
     * {@inheritdoc}
     *
     * @param TableManipulatorInterface $tableManipulator
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableManipulatorInterface $tableManipulator
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableManipulator = $tableManipulator;
    }

    /**
     * @param string $channelCode
     *
     * @return string|null
     */
    public function getUsedThemeName($channelCode)
    {
        $table = $this->getDocument()->find('css', 'table');

        $row = $this->tableManipulator->getRowWithFields($table, ['code' => $channelCode]);

        return trim($this->tableManipulator->getFieldFromRow($table, $row, 'theme')->getText());
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_backend_channel_index';
    }
}
