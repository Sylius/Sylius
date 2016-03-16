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
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorAliasingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslatorAliasingPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_aliases_theme_bundle_translator()
    {
        $this->setDefinition('sylius.theme.translation.translator', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasAlias('translator', 'sylius.theme.translation.translator');
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TranslatorAliasingPass());
    }
}
