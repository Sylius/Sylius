<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Metadata\Provider;

use Sylius\Metadata\Model\MetadataInterface;
use Sylius\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataProviderInterface
{
    /**
     * @param MetadataSubjectInterface $metadataSubject
     *
     * @return MetadataInterface|null
     */
    public function findMetadataBySubject(MetadataSubjectInterface $metadataSubject);
}
