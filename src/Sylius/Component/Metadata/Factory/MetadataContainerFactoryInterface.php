<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Factory;

use Sylius\Component\Metadata\Model\MetadataContainerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataContainerFactoryInterface
{
    /**
     * @param string $id
     *
     * @return MetadataContainerInterface
     */
    public function createIdentifiedBy($id);
}
