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

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Sylius\Bundle\ContentBundle\Document\ImagineBlock;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Vidy Videni <vidy.videni@gmail.com>
 */
final class ImagineBlockExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $imagineBlockFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    private $filterConfiguration;
    /**
     * @param FactoryInterface $imagineBlockFactory
     */
    public function __construct(FactoryInterface $imagineBlockFactory,FilterConfiguration $filterConfiguration)
    {
        $this->imagineBlockFactory = $imagineBlockFactory;
        $this->filterConfiguration = $filterConfiguration;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('label', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('name', function (Options $options) {
                    return StringInflector::nameToCode($options['title']);
                })
                ->setDefault('publishable', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setDefault('filter', function (Options $options) {
                    return $this->faker->randomElement($this->filterConfiguration->all());
                })
                ->setDefault('publishStartDate', function (Options $options) {
                    return $this->faker->dateTimeBetween('-30 days','30 days');
                }) ->setDefault('publishEndDate', function (Options $options) {
                    return $this->faker->dateTimeBetween('-30 days','30 days');
                })
                ->setRequired('image')
                ->setAllowedTypes('publishable', 'bool')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ImagineBlock $imagineBlock */
        $imagineBlock = $this->imagineBlockFactory->createNew();
        $imagineBlock->setLabel($options['label']);
        $imagineBlock->setName($options['name']);
        $imagineBlock->setPublishable($options['publishable']);
        $imagineBlock->setPublishStartDate($options['publishStartDate']);
        $imagineBlock->setPublishEndDate($options['publishEndDate']);


        return $imagineBlock;
    }
}
