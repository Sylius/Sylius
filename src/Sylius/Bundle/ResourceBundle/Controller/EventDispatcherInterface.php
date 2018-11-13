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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Model\ResourceInterface;

interface EventDispatcherInterface
{
    public function dispatch(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;

    public function dispatchMultiple(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        $resources
    ): ResourceControllerEvent;

    public function dispatchPreEvent(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;

    public function dispatchPostEvent(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;

    public function dispatchInitializeEvent(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;
}
