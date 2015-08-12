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

use Sylius\Component\Seo\Model\RootMetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataCompiler implements MetadataCompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function compile(RootMetadataInterface $rootMetadata)
    {
        $compiledMetadata = clone $rootMetadata->getMetadata();

        while ($rootMetadata->hasParent()) {
            $rootMetadata = $rootMetadata->getParent();

            $compiledMetadata->merge($rootMetadata->getMetadata());
        }

        return $compiledMetadata;
    }
}