<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Exception;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ParameterNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string $parameter
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($parameter, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Parameter with name "%s" does not exist.', $parameter);

        parent::__construct($message, $code, $previous);
    }
}
