<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchetypeFilter extends AbstractFilter
{
    const OPTION_ARCHETYPE = 'archetype';

    public function __construct($configuration)
    {
        $this->configuration = $this->resolveConfiguration($configuration);
    }

    /**
     * {@inheritdoc}
     */
    protected function filter(ArrayCollection $collection)
    {
        $returnedCollection = new ArrayCollection();

        /** @var OrderItemInterface $item */
        foreach ($collection as $item)
        {
            $archetype = $item->getProduct()->getArchetype();

            if ($archetype->getId() == $this->configuration[self::OPTION_ARCHETYPE]) {
                $returnedCollection->add($item);
            }
        }

        return $returnedCollection;
    }

    private function resolveConfiguration($configuration)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(self::OPTION_ARCHETYPE);
        $resolver->setAllowedTypes(self::OPTION_ARCHETYPE, 'int');

        return $resolver->resolve($configuration);
    }
}