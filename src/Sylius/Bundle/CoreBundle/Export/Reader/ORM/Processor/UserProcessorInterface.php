<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM\Processor;

interface UserProcessorInterface
{
    /**
     * @param array $users
     *
     * @return array
     */
    public function convert(array $users, $format);
}
