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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FlashHelperInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param string $actionName
     * @param ResourceInterface|null $resource
     */
    public function addSuccessFlash(RequestConfiguration $requestConfiguration, $actionName, ResourceInterface $resource = null);

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param string $actionName
     */
    public function addErrorFlash(RequestConfiguration $requestConfiguration, $actionName);

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceControllerEvent $event
     */
    public function addFlashFromEvent(RequestConfiguration $requestConfiguration, ResourceControllerEvent $event);
}
