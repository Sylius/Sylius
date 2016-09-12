<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Currency\Handler;

use Sylius\Component\Core\Exception\HandleException;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CompositeCurrencyChangeHandler implements CurrencyChangeHandlerInterface
{
    /**
     * @var PriorityQueue|CurrencyChangeHandlerInterface[]
     */
    private $handlers;

    public function __construct()
    {
        $this->handlers = new PriorityQueue();
    }

    /**
     * @param CurrencyChangeHandlerInterface $currencyChangeHandler
     * @param int $priority
     */
    public function addHandler(CurrencyChangeHandlerInterface $currencyChangeHandler, $priority = 0)
    {
        $this->handlers->insert($currencyChangeHandler, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($code)
    {
        if ($this->handlers->isEmpty()) {
            throw new HandleException(self::class, 'There are no handlers defined.');
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($code);
        }
    }
}
