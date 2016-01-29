<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Processor;

use Sylius\Component\Metadata\Model\MetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataProcessorInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param array $options
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata, array $options = []);
}
