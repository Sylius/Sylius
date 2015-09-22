<?php

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