<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\QueryLogger;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
interface QueryLoggerInterface
{
    /**
     * @param string $searchTerm
     * @param string $ipAddress
     */
    public function logStringQuery($searchTerm, $ipAddress);

    /**
     * @return bool
     */
    public function isEnabled();
}
