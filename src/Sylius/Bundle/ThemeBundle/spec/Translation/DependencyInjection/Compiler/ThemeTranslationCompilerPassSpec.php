<?php

namespace spec\Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeTranslationCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin ThemeTranslationCompilerPass
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeTranslationCompilerPassSpec extends FixtureAwareObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeTranslationCompilerPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_does_nothing_if_there_is_no_theme(ContainerBuilder $containerBuilder, ThemeRepositoryInterface $themeRepository)
    {
        $containerBuilder->get("sylius.repository.theme")->shouldBeCalled()->willReturn($themeRepository);
        $themeRepository->findAll()->shouldBeCalled()->willReturn([]);

        $this->process($containerBuilder);
    }

    function it_finds_translation_files_and_adds_them_to_translator(
        ContainerBuilder $containerBuilder,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $firstTheme, ThemeInterface $secondTheme,
        Definition $translatorDefinition
    ) {
        $containerBuilder->get("sylius.repository.theme")->shouldBeCalled()->willReturn($themeRepository);
        $themeRepository->findAll()->shouldBeCalled()->willReturn([$firstTheme, $secondTheme]);

        $firstTheme->getPath()->shouldBeCalled()->willReturn($this->getFirstThemePath());
        $secondTheme->getPath()->shouldBeCalled()->willReturn($this->getSecondThemePath());

        $containerBuilder->getParameter('kernel.bundles')->shouldBeCalled()->willReturn(["SampleBundle" => "/Foo/Bar"]);
        $containerBuilder->addResource(Argument::type('Symfony\Component\Config\Resource\DirectoryResource'))->shouldBeCalled();
        $containerBuilder->findDefinition('translator.default')->shouldBeCalled()->willReturn($translatorDefinition);

        $translatorDefinition->getArgument(3)->shouldBeCalled()->willReturn([
            "resource_files" => [
                "en" => [
                    "/lorem/ipsum/messages.en.yml",
                ]
            ]
        ]);
        $translatorDefinition->replaceArgument(3, [
            "resource_files" => [
                "en" => [
                    "/lorem/ipsum/messages.en.yml",
                    $this->getFirstThemePath() . '/SampleBundle/translations/messages.en.yml',
                    $this->getFirstThemePath() . '/translations/messages.en.yml',
                    $this->getSecondThemePath() . '/translations/messages.en.yml',
                ]
            ]
        ])->shouldBeCalled();

        $this->process($containerBuilder);
    }

    /**
     * @return string
     */
    private function getFirstThemePath()
    {
        return $this->getFixturePath('themes/SampleTheme');
    }

    /**
     * @return string
     */
    private function getSecondThemePath()
    {
        return $this->getFixturePath('themes/SecondSampleTheme');
    }
}