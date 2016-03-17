<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * {@inheritdoc}
     *
     * @param TableAccessorInterface $tableAccessor
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function isThereShippingMethodNamed($name)
    {
        if (null === $table = $this->getDocument()->find('css', 'table')) {
            return false;
        }

        try {
            $row = $this->tableAccessor->getRowWithFields($table, ['name' => $name]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return 1 === $row;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_backend_shipping_method_index';
    }
}
