<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Tests\Functional;

use Sylius\Bundle\UiBundle\SyliusUiBundle;
use Sylius\Bundle\UiBundle\Tests\Functional\src\SomeTwigComponent;
use Sylius\TwigExtra\Symfony\SyliusTwigExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SyliusUiBundle(),
            new WebpackEncoreBundle(),
            new TwigComponentBundle(),
            new SyliusTwigExtraBundle(),
        ];
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->register(SomeTwigComponent::class)->addTag('twig.component', ['template' => 'blocks/twigComponent/someTwigComponent.html.twig']);

        $container->loadFromExtension('framework', [
            'secret' => 'S0ME_SECRET',
        ]);

        $container->loadFromExtension('webpack_encore', [
            'output_path' => '%kernel.project_dir%/public/build',
        ]);
    }
}
