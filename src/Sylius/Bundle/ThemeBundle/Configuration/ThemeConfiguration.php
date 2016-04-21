<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNodeDefinition = $treeBuilder->root('sylius_theme');

        $rootNodeDefinition->ignoreExtraKeys();

        $this->addRequiredNameField($rootNodeDefinition);
        $this->addOptionalTitleField($rootNodeDefinition);
        $this->addOptionalDescriptionField($rootNodeDefinition);
        $this->addOptionalPathField($rootNodeDefinition);
        $this->addOptionalParentsList($rootNodeDefinition);
        $this->addOptionalScreenshotsList($rootNodeDefinition);
        $this->addOptionalAuthorsList($rootNodeDefinition);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addRequiredNameField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('name')->isRequired()->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalTitleField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('title')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalDescriptionField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('description')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalPathField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('path')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalParentsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $parentsNodeDefinition = $rootNodeDefinition->children()->arrayNode('parents');
        $parentsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
                ->prototype('scalar')
                ->cannotBeEmpty()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalScreenshotsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $screenshotsNodeDefinition = $rootNodeDefinition->children()->arrayNode('screenshots');
        $screenshotsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $screenshotNodeDefinition */
        $screenshotNodeDefinition = $screenshotsNodeDefinition->prototype('array');

        $screenshotNodeDefinition
            ->validate()
                ->ifTrue(function ($screenshot) {
                    return [] === $screenshot || ['path' => ''] === $screenshot;
                })
                ->thenInvalid('Screenshot cannot be empty!')
        ;
        $screenshotNodeDefinition
            ->beforeNormalization()
                ->ifString()
                ->then(function ($value) {
                    return ['path' => $value];
                })
        ;

        $screenshotNodeBuilder = $screenshotNodeDefinition->children();
        $screenshotNodeBuilder->scalarNode('path')->isRequired();
        $screenshotNodeBuilder->scalarNode('title')->cannotBeEmpty();
        $screenshotNodeBuilder->scalarNode('description')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalAuthorsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $authorsNodeDefinition = $rootNodeDefinition->children()->arrayNode('authors');
        $authorsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $authorNodeDefinition */
        $authorNodeDefinition = $authorsNodeDefinition->prototype('array');
        $authorNodeDefinition
            ->validate()
                ->ifTrue(function ($author) {
                    return [] === $author;
                })
                ->thenInvalid('Author cannot be empty!')
        ;

        $authorNodeBuilder = $authorNodeDefinition->children();
        $authorNodeBuilder->scalarNode('name')->cannotBeEmpty();
        $authorNodeBuilder->scalarNode('email')->cannotBeEmpty();
        $authorNodeBuilder->scalarNode('homepage')->cannotBeEmpty();
        $authorNodeBuilder->scalarNode('role')->cannotBeEmpty();
    }
}
