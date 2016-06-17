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
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractResourceFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $resourceManager;

    /**
     * @var string
     */
    private $nodeName;

    /**
     * @var string|null
     */
    private $identifierField;

    /**
     * @param ObjectManager $resourceManager
     * @param string $nodeName
     * @param string|null $identifierField
     */
    public function __construct(ObjectManager $resourceManager, $nodeName, $identifierField = null)
    {
        $this->resourceManager = $resourceManager;
        $this->nodeName = $nodeName;
        $this->identifierField = $identifierField;
    }

    /**
     * @param array $options
     */
    final public function load(array $options)
    {
        $optionsResolver = $this->createConfiguredOptionsResolver($options);

        $resourcesOptions = array_merge($options[$this->nodeName], $this->generateResourcesOptions($options['random']));
        foreach ($resourcesOptions as $resourceOptions) {
            $resource = $this->loadResource($optionsResolver->resolve($resourceOptions));

            $this->resourceManager->persist($resource);
        }

        $this->resourceManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    final public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $optionsNode = $treeBuilder->root($this->getName());

        $optionsNode->children()->integerNode('random')->min(0)->defaultValue(0);

        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode($this->nodeName);

        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode
            ->requiresAtLeastOneElement()
            ->prototype('array')
        ;

        if (null !== $this->identifierField) {
            $resourceNode
                ->children()
                ->scalarNode($this->identifierField)
                ->isRequired()
                ->cannotBeEmpty()
            ;

            $resourceNode
                ->beforeNormalization()
                ->ifString()
                ->then(function ($identifier) {
                    return [$this->identifierField => $identifier];
                })
            ;
        }

        $resourceNode->ignoreExtraKeys(false);

        $this->configureOptionsNode($optionsNode);
        $this->configureResourceNode($resourceNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $optionsNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        // empty
    }

    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        // empty
    }

    /**
     * @param array $options
     * @param OptionsResolver $optionsResolver
     */
    protected function configureResourceOptionsResolver(array $options, OptionsResolver $optionsResolver)
    {
        // empty
    }

    /**
     * Normalizes empty array to all resources.
     * Normalizes identifiers array to matched resources.
     *
     * @param RepositoryInterface $repository
     * @param string $searchedField
     *
     * @return \Closure
     */
    final protected static function createResourcesNormalizer(RepositoryInterface $repository, $searchedField = 'code')
    {
        return function (Options $options, array $identifiers) use ($repository, $searchedField) {
            if (0 === count($identifiers)) {
                return $repository->findAll();
            }

            $resources = [];
            foreach ($identifiers as $identifier) {
                $resource = $repository->findOneBy([$searchedField => $identifier]);

                Assert::notNull($resource);

                $resources[] = $resource;
            }

            return $resources;
        };
    }

    /**
     * Normalizes null to a random resource.
     * Normalizes identifier to matched resource.
     *
     * @param RepositoryInterface $repository
     * @param string $searchedField
     *
     * @return \Closure
     */
    final protected static function createResourceNormalizer(RepositoryInterface $repository, $searchedField = 'code')
    {
        return function (Options $options, $identifier) use ($repository, $searchedField) {
            if (null === $identifier) {
                $resources = $repository->findAll();

                return $resources[array_rand($resources)];
            }

            $resource = $repository->findOneBy([$searchedField => $identifier]);

            Assert::notNull($resource);

            return $resource;
        };
    }

    /**
     * @param array $options
     *
     * @return ResourceInterface
     */
    abstract protected function loadResource(array $options);

    /**
     * @param int $amount
     *
     * @return array
     */
    abstract protected function generateResourcesOptions($amount);

    /**
     * @param array $options
     *
     * @return OptionsResolver
     */
    private function createConfiguredOptionsResolver(array $options)
    {
        unset($options[$this->nodeName]);

        $optionsResolver = new OptionsResolver();
        $this->configureResourceOptionsResolver($options, $optionsResolver);

        return $optionsResolver;
    }
}
