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

use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class RandomReviewsFixture extends AbstractFixture
{
    /**
     * @var AbstractResourceFixture
     */
    private $productReviewFixture;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param AbstractResourceFixture $productReviewFixture
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        AbstractResourceFixture $productReviewFixture,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productReviewFixture = $productReviewFixture;
        $this->productRepository = $productRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('amount')
                ->setAllowedTypes('amount', 'int')
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $this->productReviewFixture->load(['random' => $options['amount']]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNode
            ->children()
            ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }
}
