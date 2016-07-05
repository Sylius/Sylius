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

use Sylius\Bundle\ContentBundle\Document\Route;
use Sylius\Bundle\ContentBundle\Document\StaticContent;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RouteExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $routeFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $routeFactory
     */
    public function __construct(FactoryInterface $routeFactory, RepositoryInterface $staticContentRepository)
    {
        $this->routeFactory = $routeFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return StringInflector::nameToCode($this->faker->words(3, true));
                })
                ->setDefault('content', LazyOption::randomOne($staticContentRepository))
                ->setAllowedTypes('content', ['string', StaticContent::class])
                ->setNormalizer('content', LazyOption::findOneBy($staticContentRepository, 'name'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var Route $route */
        $route = $this->routeFactory->createNew();
        $route->setName($options['name']);
        $route->setContent($options['content']);

        return $route;
    }
}
