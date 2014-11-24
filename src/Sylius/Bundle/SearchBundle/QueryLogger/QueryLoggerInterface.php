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
 * @author agounaris <agounaris@gmail.com>
 */
interface QueryLoggerInterface
{

    /**
     * @param $searchTerm
     * @param $ipAddress
     */
    public function logStringQuery($searchTerm, $ipAddress);

    /**
     * @return mixed
     */
    public function isEnabled();

} 