<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Liverbool <liverbool@gmail.com>
 */
interface RestorableInterface
{
    /**
     * Restore deleted resource
     */
    public function restore();
}
