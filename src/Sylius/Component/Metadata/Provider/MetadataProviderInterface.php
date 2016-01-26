<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Provider;

use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

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
