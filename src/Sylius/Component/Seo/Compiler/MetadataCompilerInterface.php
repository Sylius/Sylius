<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Seo\Compiler;

use Sylius\Component\Seo\Model\MetadataInterface;
use Sylius\Component\Seo\Model\RootMetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataCompilerInterface
{
    /**
     * @param RootMetadataInterface $rootMetadata
     *
     * @return MetadataInterface
     */
    public function compile(RootMetadataInterface $rootMetadata);
}