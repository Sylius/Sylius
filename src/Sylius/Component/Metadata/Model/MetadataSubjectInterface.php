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

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataSubjectInterface
{
    /**
     * Metadata class identifier is usually a class name (not FQCN), eg. "MetadataSubject".
     *
     * @return string
     */
    public function getMetadataClassIdentifier();

    /**
     * Metadata identifier is usually a class name (not FQCN) and ID joined by dash, eg. "MetadataSubject-42".
     *
     * @return string
     */
    public function getMetadataIdentifier();
}
