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

namespace Sylius\Component\Resource\Repository\Exception;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ExistingResourceException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Given resource already exists in the repository.');
    }
}
