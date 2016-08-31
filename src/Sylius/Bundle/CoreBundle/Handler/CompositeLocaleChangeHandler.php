<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Handler;

use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\LocaleChangeHandlerInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CompositeLocaleChangeHandler implements LocaleChangeHandlerInterface
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
            throw new HandleException(self::class, 'There is no defined handlers.');
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($code);
        }
    }
}
