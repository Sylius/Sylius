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

use Symfony\Component\HttpFoundation\ParameterBag;

class Parameters extends ParameterBag
{
    /**
     * {@inheritdoc}
     */
    public function get($path, $default = null)
    {
        $result = parent::get($path, $default);

        if (null === $result && $default !== null && $this->has($path)) {
            $result = $default;
        }

        return $result;
    }
}
