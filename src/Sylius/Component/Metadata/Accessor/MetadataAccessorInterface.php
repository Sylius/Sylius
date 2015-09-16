<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Accessor;

use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataAccessorInterface
{
    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string|null $propertyPath
     *
     * @return mixed
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $propertyPath = null);
}
