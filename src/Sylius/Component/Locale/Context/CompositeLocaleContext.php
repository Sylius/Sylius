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

namespace Sylius\Component\Locale\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeLocaleContext implements LocaleContextInterface
{
    /**
     * @var PriorityQueue|LocaleContextInterface[]
     */
    private $localeContexts;

    public function __construct()
    {
        $this->localeContexts = new PriorityQueue();
    }

    /**
     * @param LocaleContextInterface $localeContext
     * @param int $priority
     */
    public function addContext(LocaleContextInterface $localeContext, int $priority = 0): void
    {
        $this->localeContexts->insert($localeContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): string
    {
        $lastException = null;

        foreach ($this->localeContexts as $localeContext) {
            try {
                return $localeContext->getLocaleCode();
            } catch (LocaleNotFoundException $exception) {
                $lastException = $exception;

                continue;
            }
        }

        throw new LocaleNotFoundException(null, $lastException);
    }
}
