<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

use Zend\Stdlib\PriorityQueue;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
    public function addContext(LocaleContextInterface $localeContext, $priority = 0)
    {
        $this->localeContexts->insert($localeContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
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
