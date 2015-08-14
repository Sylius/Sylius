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

use Sylius\Component\Product\Model\Archetype as BaseArchetype;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Archetype extends BaseArchetype implements ArchetypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMetadataClassIdentifier()
    {
        return 'Archetype';
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataIdentifier()
    {
        return $this->getMetadataClassIdentifier() . '-' . $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass()
    {
        return 'Sylius\Component\Product\Model\ArchetypeTranslation';
    }
}
