<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Registry;

/**
 * This exception should be thrown by service registry
 * when given type already exists.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExistingServiceException extends \InvalidArgumentException
{
    public function __construct($type)
    {
        parent::__construct(sprintf('Service of type "%s" already exist.', $type));
    }
}
