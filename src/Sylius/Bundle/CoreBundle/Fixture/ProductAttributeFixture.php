<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ProductAttributeFixture extends AbstractResourceFixture
{
    /**
     * @var array
     */
    private $attributeTypes;

    /**
     * @param ObjectManager $objectManager
     * @param ExampleFactoryInterface $exampleFactory
     * @param array $attributeTypes
     */
    public function __construct(ObjectManager $objectManager, ExampleFactoryInterface $exampleFactory, array $attributeTypes)
    {
        parent::__construct($objectManager, $exampleFactory);

        $this->attributeTypes = array_keys($attributeTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'product_attribute';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->enumNode('type')->values($this->attributeTypes)->cannotBeEmpty()->end()
                ->variableNode('configuration')->end()
        ;
    }
}
