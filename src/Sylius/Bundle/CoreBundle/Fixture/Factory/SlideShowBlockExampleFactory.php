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

use Doctrine\ODM\PHPCR\DocumentManager;
use Sylius\Bundle\ContentBundle\Document\SlideshowBlock;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Vidy Videni <vidy.videni@gmail.com>
 */
final class SlideShowBlockExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $slideshowBlockFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @param FactoryInterface $slideshowBlockFactory
     */
    public function __construct(FactoryInterface $slideshowBlockFactory, DocumentManager $documentManager)
    {
        $this->slideshowBlockFactory = $slideshowBlockFactory;
        $this->documentManager = $documentManager;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('title', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('name', function (Options $options) {
                    return StringInflector::nameToCode($options['title']);
                })
                ->setDefault('publishable', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setDefault('parent', function (Options $options) {
                    return '/cms/blocks';
                })
                ->setDefault('publishStartDate', function (Options $options) {
                    return $this->faker->dateTimeBetween('-30 days', '30 days');
                })->setDefault('publishEndDate', function (Options $options) {
                    return $this->faker->dateTimeBetween('-30 days', '30 days');
                })
                ->setAllowedTypes('publishable', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var SlideshowBlock $slideshowBlock */
        $slideshowBlock = $this->slideshowBlockFactory->createNew();
        $slideshowBlock->setTitle($options['title']);
        $slideshowBlock->setName($options['name']);
        $slideshowBlock->setPublishable($options['publishable']);
        $slideshowBlock->setEnabled($options['enabled']);
        $slideshowBlock->setPublishStartDate($options['publishStartDate']);
        $slideshowBlock->setPublishEndDate($options['publishEndDate']);

        $parentDocument = $this->documentManager->find(null, $options['parent']);

        $slideshowBlock->setParentDocument($parentDocument);

        return $slideshowBlock;
    }
}
