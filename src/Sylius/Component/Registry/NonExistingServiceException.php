<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Registry;

/**
 * This exception should be thrown by service registry
 * when given service type does not exist.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NonExistingServiceException extends \InvalidArgumentException
{
    public function __construct($context, $type, array $existingServices)
    {
        parent::__construct(sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst($context),
            $type,
            $context,
            implode('", "', $existingServices)
        ));
    }
}
