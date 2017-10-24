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

namespace Sylius\Bundle\ThemeBundle\Tests\Translation\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorResourceProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class TranslatorResourceProviderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_copies_resource_files_from_symfony_translator_to_sylius_resource_provider(): void
    {
        $symfonyTranslatorDefinition = new Definition(null, [
            null,
            null,
            [],
            ['resource_files' => [
                'en' => ['/resources/messages.en.yml', '/resources/alerts.en.yml'],
                'es' => ['/resources/messages.es.yml'],
            ]],
        ]);
        $this->setDefinition('translator.default', $symfonyTranslatorDefinition);

        $this->setDefinition('sylius.theme.translation.resource_provider.default', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.resource_provider.default',
            0,
            ['/resources/messages.en.yml', '/resources/alerts.en.yml', '/resources/messages.es.yml']
        );
    }

    /**
     * @test
     */
    public function it_merges_copied_resource_files_from_symfony_translator_with_existing_resource_files_from_sylius_resource_provider(): void
    {
        $symfonyTranslatorDefinition = new Definition(null, [
            null,
            null,
            [],
            ['resource_files' => ['en' => ['/resources/messages.en.yml']]],
        ]);
        $this->setDefinition('translator.default', $symfonyTranslatorDefinition);

        $this->setDefinition('sylius.theme.translation.resource_provider.default', new Definition(null, [
            ['/resources/alerts.en.yml'],
        ]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.resource_provider.default',
            0,
            ['/resources/alerts.en.yml', '/resources/messages.en.yml']
        );
    }

    /**
     * @test
     */
    public function it_does_not_copy_anything_if_symfony_translator_does_not_have_resource_files(): void
    {
        $symfonyTranslatorDefinition = new Definition(null, [
            null,
            null,
            [],
            ['cache_dir' => '/foo/bar'],
        ]);
        $this->setDefinition('translator.default', $symfonyTranslatorDefinition);

        $this->setDefinition('sylius.theme.translation.resource_provider.default', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.resource_provider.default',
            0,
            []
        );
    }

    /**
     * @test
     */
    public function it_copies_resource_files_from_symfony_translator_33_to_sylius_resource_provider(): void
    {
        $symfonyTranslatorDefinition = new Definition(null, [
            null,
            null,
            [],
            [],
            ['resource_files' => [
                'en' => ['/resources/messages.en.yml', '/resources/alerts.en.yml'],
                'es' => ['/resources/messages.es.yml'],
            ]],
        ]);
        $this->setDefinition('translator.default', $symfonyTranslatorDefinition);

        $this->setDefinition('sylius.theme.translation.resource_provider.default', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.resource_provider.default',
            0,
            ['/resources/messages.en.yml', '/resources/alerts.en.yml', '/resources/messages.es.yml']
        );
    }

    /**
     * @test
     */
    public function it_does_not_crash_if_definition_does_not_have_resource_files_at_all(): void
    {
        $symfonyTranslatorDefinition = new Definition(null, [null, null]);
        $this->setDefinition('translator.default', $symfonyTranslatorDefinition);

        $this->setDefinition('sylius.theme.translation.resource_provider.default', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.theme.translation.resource_provider.default',
            0,
            []
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TranslatorResourceProviderPass());
    }
}
