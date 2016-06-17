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
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractResourceFixture extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    private $resourceManager;

    /**
     * @var string
     */
    private $resourceNodeName;

    /**
     * @var string|null
     */
    private $resourceNodeIdentifier;

    /**
     * @param ObjectManager $resourceManager
     * @param string $resourceNodeName
     * @param string|null $resourceNodeIdentifier
     */
    public function __construct(ObjectManager $resourceManager, $resourceNodeName, $resourceNodeIdentifier = null)
    {
        $this->resourceManager = $resourceManager;
        $this->resourceNodeName = $resourceNodeName;
        $this->resourceNodeIdentifier = $resourceNodeIdentifier;
    }

    /**
     * @param array $options
     */
    final public function load(array $options)
    {
        $optionsResolver = new OptionsResolver();

        $this->configureOptionsResolver($optionsResolver);

        foreach ($options[$this->resourceNodeName] as $resourceOptions) {
            $resource = $this->loadResource($optionsResolver->resolve($resourceOptions));

            $this->resourceManager->persist($resource);
        }

        $this->resourceManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    final protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode($this->resourceNodeName);

        $resourcesNode
            ->beforeNormalization()
                ->ifTrue(function ($value) {
                    return is_numeric($value) && 0 !== (int) $value;
                })
                ->then(function ($amount) {
                    return $this->generateResourcesConfigurations($amount);
                })
            ->end()
        ;

        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('array')
        ;


        if (null !== $this->resourceNodeIdentifier) {
            $resourceNode
                ->children()
                    ->scalarNode($this->resourceNodeIdentifier)
                        ->isRequired()
            ;

            $resourceNode
                ->beforeNormalization()
                    ->ifString()
                    ->then(function ($identifier) {
                        return [$this->resourceNodeIdentifier => $identifier];
                    })
            ;
        }

        $resourceNode->ignoreExtraKeys(false);

        $this->configureResourceNode($resourceNode);
    }

    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        // empty
    }

    /**
     * @param OptionsResolver $optionsResolver
     */
    protected function configureOptionsResolver(OptionsResolver $optionsResolver)
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
     * @param array $resourceOptions
     *
     * @return ResourceInterface
     */
    abstract protected function loadResource(array $resourceOptions);

    /**
     * @param int $amount
     *
     * @return array
     */
    abstract protected function generateResourcesConfigurations($amount);
}
