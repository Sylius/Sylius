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
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class MetadataContainerFactory implements MetadataContainerFactoryInterface, FactoryInterface
{
    /**
     * @var string
     */
    private $metadataContainerClass;

    /**
     * @param string $metadataContainerClass
     */
    public function __construct($metadataContainerClass)
    {
        $this->metadataContainerClass = $metadataContainerClass;
    }

    /**
     * {@inheritdoc}
     *
     * @return MetadataContainerInterface
     */
    public function createNew()
    {
        return new $this->metadataContainerClass();
    }

    /**
     * {@inheritdoc}
     */
    public function createIdentifiedBy($id)
    {
        $metadataContainer = $this->createNew();
        $metadataContainer->setId($id);

        return $metadataContainer;
    }
}
