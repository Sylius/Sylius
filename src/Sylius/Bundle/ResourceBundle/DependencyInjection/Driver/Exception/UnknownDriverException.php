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

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class UnknownDriverException extends \Exception
{
    public function __construct($driver)
    {
        parent::__construct(sprintf(
            'Unknown driver "%s".',
            $driver
        ));
    }
}
