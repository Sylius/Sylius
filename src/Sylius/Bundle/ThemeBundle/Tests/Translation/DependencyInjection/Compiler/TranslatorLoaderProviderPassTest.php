<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Translation\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorLoaderProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslatorLoaderProviderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_translation_loaders_to_sylius_loader_provider()
    {
        $this->setDefinition('sylius.theme.translation.loader_provider', new Definition(null, [[]]));

        $translationLoaderDefinition = new Definition();
        $translationLoaderDefinition->addTag('translation.loader', ['alias' => 'yml']);
        $this->setDefinition('translation.loader.yml', $translationLoaderDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.loader_provider',
            0,
            ['yml' => new Reference('translation.loader.yml')]
        );
    }

    /**
     * @test
     */
    public function it_adds_translation_loaders_with_its_legacy_alias_to_sylius_loader_provider()
    {
        $this->setDefinition('sylius.theme.translation.loader_provider', new Definition(null, [[]]));

        $translationLoaderDefinition = new Definition();
        $translationLoaderDefinition->addTag('translation.loader', ['alias' => 'xlf', 'legacy-alias' => 'xliff']);
        $this->setDefinition('translation.loader.xliff', $translationLoaderDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.loader_provider',
            0,
            ['xlf' => new Reference('translation.loader.xliff'), 'xliff' => new Reference('translation.loader.xliff')]
        );
    }

    /**
     * @test
     */
    public function it_adds_translation_loaders_using_only_the_first_tag_alias()
    {
        $this->setDefinition('sylius.theme.translation.loader_provider', new Definition(null, [[]]));

        $translationLoaderDefinition = new Definition();
        $translationLoaderDefinition->addTag('translation.loader', ['alias' => 'yml']);
        $translationLoaderDefinition->addTag('translation.loader', ['alias' => 'yaml']);
        $this->setDefinition('translation.loader.yml', $translationLoaderDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.loader_provider',
            0,
            ['yml' => new Reference('translation.loader.yml')]
        );
    }

    /**
     * @test
     */
    public function it_does_not_force_the_existence_of_translation_loaders()
    {
        $this->setDefinition('sylius.theme.translation.loader_provider', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.loader_provider',
            0,
            []
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TranslatorLoaderProviderPass());
    }
}
