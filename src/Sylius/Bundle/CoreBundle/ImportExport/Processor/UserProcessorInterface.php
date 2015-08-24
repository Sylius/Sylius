<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\ImportExport\Processor;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UserProcessorInterface
{
    /**
     * @param array  $users
     * @param string $format
     *
     * @return array
     */
    public function convert(array $users, $format);

    /**
     * @param array  $users
     * @param string $format
     *
     * @return array
     */
    public function revert(array $users, $format);
}
