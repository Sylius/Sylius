<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Repository;

use Sylius\Bundle\ContentBundle\Document\StaticContent;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface StaticContentRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return StaticContent|null
     */
    public function findOneByName($name);
}
