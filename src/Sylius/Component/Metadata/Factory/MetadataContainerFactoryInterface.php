<?php

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
