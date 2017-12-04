<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Currency\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var PriorityQueue|CurrencyContextInterface[]
     */
    private $currencyContexts;

    public function __construct()
    {
        $this->currencyContexts = new PriorityQueue();
    }

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param int $priority
     */
    public function addContext(CurrencyContextInterface $currencyContext, int $priority = 0): void
    {
        $this->currencyContexts->insert($currencyContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode(): string
    {
        $lastException = null;

        foreach ($this->currencyContexts as $currencyContext) {
            try {
                return $currencyContext->getCurrencyCode();
            } catch (CurrencyNotFoundException $exception) {
                $lastException = $exception;

                continue;
            }
        }

        throw new CurrencyNotFoundException(null, $lastException);
    }
}
