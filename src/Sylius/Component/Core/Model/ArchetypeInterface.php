<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Product\Model\ArchetypeInterface as BaseArchetypeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ArchetypeInterface extends BaseArchetypeInterface, MetadataSubjectInterface
{
}
