<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Archetype\Model;

use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Variation\Model\VariableInterface;

/**
 * The class that is the result of copies made from an archetype, should
 * implement this interface.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
interface ArchetypeSubjectInterface extends AttributeSubjectInterface, VariableInterface
{
    /**
     * @return null|ArchetypeInterface
     */
    public function getArchetype();

    /**
     * @param ArchetypeInterface $archetype
     */
    public function setArchetype(ArchetypeInterface $archetype = null);
}
