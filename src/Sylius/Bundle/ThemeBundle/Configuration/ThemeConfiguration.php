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

namespace Sylius\Bundle\ThemeBundle\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ThemeConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('sylius_theme');
            $rootNodeDefinition = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNodeDefinition = $treeBuilder->root('sylius_theme');
        }

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

    private function addRequiredNameField(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $rootNodeDefinition->children()->scalarNode('name')->isRequired()->cannotBeEmpty();
    }

    private function addOptionalTitleField(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $rootNodeDefinition->children()->scalarNode('title')->cannotBeEmpty();
    }

    private function addOptionalDescriptionField(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $rootNodeDefinition->children()->scalarNode('description')->cannotBeEmpty();
    }

    private function addOptionalPathField(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $rootNodeDefinition->children()->scalarNode('path')->cannotBeEmpty();
    }

    private function addOptionalParentsList(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $parentsNodeDefinition = $rootNodeDefinition->children()->arrayNode('parents');
        $parentsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
                ->scalarPrototype()
                ->cannotBeEmpty()
        ;
    }

    private function addOptionalScreenshotsList(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $screenshotsNodeDefinition = $rootNodeDefinition->children()->arrayNode('screenshots');
        $screenshotsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $screenshotNodeDefinition */
        $screenshotNodeDefinition = $screenshotsNodeDefinition->arrayPrototype();

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

    private function addOptionalAuthorsList(ArrayNodeDefinition $rootNodeDefinition): void
    {
        $authorsNodeDefinition = $rootNodeDefinition->children()->arrayNode('authors');
        $authorsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $authorNodeDefinition */
        $authorNodeDefinition = $authorsNodeDefinition->arrayPrototype();
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
