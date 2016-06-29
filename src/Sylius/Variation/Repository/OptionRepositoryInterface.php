<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Variation\Repository;

use Sylius\Resource\Repository\RepositoryInterface;
use Sylius\Variation\Model\OptionInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OptionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return OptionInterface|null
     */
    public function findOneByName($name);
}
