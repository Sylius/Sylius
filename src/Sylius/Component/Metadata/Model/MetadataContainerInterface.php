<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataContainerInterface extends ResourceInterface, TranslatableInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return MetadataInterface
     */
    public function getMetadata();

    /**
     * @param MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata);
}
