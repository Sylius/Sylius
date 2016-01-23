<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeAwareSourcesPass;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin ThemeAwareSourcesPass
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeAwareSourcesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeAwareSourcesPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_does_nothing_if_there_is_no_theme(
        ContainerBuilder $containerBuilder,
        ThemeRepositoryInterface $themeRepository,
        TranslationFilesFinderInterface $translationFilesFinder
    ) {
        $containerBuilder->get('sylius.theme.repository')->willReturn($themeRepository);
        $themeRepository->findAll()->willReturn([]);

        $containerBuilder->get('sylius.theme.translation.files_finder')->willReturn($translationFilesFinder);

        $this->process($containerBuilder);
    }

    function it_finds_translation_files_and_adds_them_to_translator(
        ContainerBuilder $containerBuilder,
        ThemeRepositoryInterface $themeRepository,
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        Definition $translatorDefinition
    ) {
        $containerBuilder->get('sylius.theme.repository')->willReturn($themeRepository);
        $themeRepository->findAll()->willReturn([$firstTheme, $secondTheme]);
        
        $containerBuilder->get('sylius.theme.translation.files_finder')->willReturn($translationFilesFinder);
        $translationFilesFinder->findTranslationFiles($firstTheme)->willReturn([
            '/theme1/SampleBundle/translations/messages.en.yml',
            '/theme1/translations/messages.pl.yml',
        ]);
        $translationFilesFinder->findTranslationFiles($secondTheme)->willReturn([
            '/theme2/translations/messages.en.yml',
        ]);

        $containerBuilder->findDefinition('translator.default')->willReturn($translatorDefinition);

        $translatorDefinition->getArgument(3)->willReturn([
            'resource_files' => [
                'en' => [
                    '/lorem/ipsum/messages.en.yml',
                ],
            ],
        ]);
        $translatorDefinition->replaceArgument(3, [
            'resource_files' => [
                'en' => [
                    '/lorem/ipsum/messages.en.yml',
                    '/theme1/SampleBundle/translations/messages.en.yml',
                    '/theme2/translations/messages.en.yml',
                ],
                'pl' => [
                    '/theme1/translations/messages.pl.yml',
                ],
            ]
        ])->shouldBeCalled();

        $containerBuilder->addResource(Argument::type(FileResource::class))->shouldBeCalledTimes(3);

        $this->process($containerBuilder);
    }
}
