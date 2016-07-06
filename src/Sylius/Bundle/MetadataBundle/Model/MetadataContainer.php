<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Model;

use Sylius\Component\Metadata\Model\MetadataContainer as BaseMetadataContainer;

/**
 * This overridden class is required because we override the MetadataContainerTranslation class
 * and need a consistent Doctrine Mapping namespace
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContainer extends BaseMetadataContainer
{
}
