<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Currency\Context;

use Laminas\Stdlib\PriorityQueue;

final class CompositeCurrencyContext implements CurrencyContextInterface
{
    /** @var PriorityQueue<CurrencyContextInterface> */
    private PriorityQueue $currencyContexts;

    public function __construct()
    {
        $this->currencyContexts = new PriorityQueue();
    }

    public function addContext(CurrencyContextInterface $currencyContext, int $priority = 0): void
    {
        $this->currencyContexts->insert($currencyContext, $priority);
    }

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
