<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingCategoryFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    private $shippingCategoryFactory;

    /**
     * @var ObjectManager
     */
    private $shippingCategoryManager;

    /**
     * @var \Faker\Generator
     */
    private $defaultFaker;

    /**
     * @param FactoryInterface $shippingCategoryFactory
     * @param ObjectManager $shippingCategoryManager
     */
    public function __construct(
        FactoryInterface $shippingCategoryFactory,
        ObjectManager $shippingCategoryManager
    ) {

        $this->shippingCategoryFactory = $shippingCategoryFactory;
        $this->shippingCategoryManager = $shippingCategoryManager;

        $this->defaultFaker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        foreach ($options['shipping_categories'] as $name) {
            $shippingCategory = $this->createShippingCategory($name, $this->defaultFaker->paragraph);

            $this->shippingCategoryManager->persist($shippingCategory);
        }

        $this->shippingCategoryManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_category';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNodeBuilder = $optionsNode->children();

        /** @var ArrayNodeDefinition $shippingCategoriesNode */
        $shippingCategoriesNode = $optionsNodeBuilder->arrayNode('shipping_categories');
        $shippingCategoriesNode
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->beforeNormalization()
                ->ifTrue(function ($value) {
                    return is_numeric($value) && 0 !== (int) $value;
                })
                ->then(function ($amount) {
                    $names = [];
                    for ($i = 0; $i < (int) $amount; ++$i) {
                        $names[] = $this->defaultFaker->words(3, true);
                    }

                    return $names;
                })
        ;
        $shippingCategoriesNode->prototype('scalar');
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return ShippingCategoryInterface
     */
    private function createShippingCategory($name, $description)
    {
        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $this->shippingCategoryFactory->createNew();

        $shippingCategory->setCode(StringInflector::nameToCode($name));
        $shippingCategory->setName($name);
        $shippingCategory->setDescription($description);

        return $shippingCategory;
    }
}
