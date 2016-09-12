<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Product\Model\OptionInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OptionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return OptionInterface[]
     */
    public function findByName($name, $locale);
}
