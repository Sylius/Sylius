<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Order;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\TableManipulatorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class OrderPaymentsPage extends SymfonyPage implements OrderPaymentsPageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'table' => '.table'
    ];

    /**
     * @var TableManipulatorInterface
     */
    private $tableManipulator;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
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
     * {@inheritdoc}
     */
    public function clickPayButtonForGivenPayment(PaymentInterface $payment)
    {
        $table = $this->getElement('table');
        $row = $this->tableManipulator->getRowWithFields($this->getElement('table'), ['#' => $payment->getId()]);
        $actions = $this->tableManipulator->getFieldFromRow($table, $row, 'Action');

        $actions->clickLink('Pay');
    }

    /**
     * {@inheritdoc}
     */
    public function countPaymentWithSpecificState($state)
    {
        $rows = $this->tableManipulator->getRowsWithFields($this->getElement('table'), ['state' => $state]);

        return count($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function waitForResponse($timeout, array $parameters)
    {
        $this->getDocument()->waitFor($timeout, function () use ($parameters) {
            return $this->isOpen($parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = [])
    {
        if (!isset($urlParameters['number'])) {
            throw new \InvalidArgumentException(sprintf('This page %s requires order number to be passed as parameter', self::class));
        }

        return parent::getUrl($urlParameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_order_payment_index';
    }
}
