<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Locale\Handler;

use Sylius\Component\Core\Exception\HandleException;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeLocaleChangeHandler implements LocaleChangeHandlerInterface
{
    /**
     * @var PriorityQueue|LocaleChangeHandlerInterface[]
     */
    private $handlers;

    public function __construct()
    {
        $this->handlers = new PriorityQueue();
    }

    /**
     * @param LocaleChangeHandlerInterface $localeChangeHandler
     * @param int $priority
     */
    public function addHandler(LocaleChangeHandlerInterface $localeChangeHandler, $priority = 0)
    {
        $this->handlers->insert($localeChangeHandler, $priority);
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
