<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingCategoryExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $shippingCategoryFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $shippingCategoryFactory
     */
    public function __construct(FactoryInterface $shippingCategoryFactory)
    {
        $this->shippingCategoryFactory = $shippingCategoryFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('code', function (Options $options) {
                    return StringInflector::nameToCode($options['name']);
                })
                ->setDefault('description', function (Options $options) {
                    return $this->faker->paragraph;
                })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);
        
        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $this->shippingCategoryFactory->createNew();

        $shippingCategory->setCode($options['code']);
        $shippingCategory->setName($options['name']);
        $shippingCategory->setDescription($options['description']);

        return $shippingCategory;
    }
}
