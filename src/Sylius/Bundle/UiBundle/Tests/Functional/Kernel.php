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

namespace Sylius\Bundle\UiBundle\Tests\Functional;

use Sonata\BlockBundle\SonataBlockBundle;
use Sylius\Bundle\UiBundle\SyliusUiBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SonataBlockBundle(),
            new SyliusUiBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'S0ME_SECRET',
        ]);

        $containerBuilder->loadFromExtension('security', ['firewalls' => ['main' => ['anonymous' => null]]]);

        $containerBuilder->loadFromExtension(
            'sonata_block',
            ['blocks' => ['sonata.block.service.template' => ['settings' => ['context' => null]]]],
        );

        $containerBuilder->loadFromExtension('sylius_ui', ['events' => [
            'first_event' => [
                'blocks' => [
                    'third' => ['template' => 'blocks/txt/third.txt.twig', 'priority' => -5],
                    'first' => ['template' => 'blocks/txt/first.txt.twig', 'priority' => 5],
                    'second' => 'blocks/txt/second.txt.twig',
                ],
            ],
            'second_event' => [
                'blocks' => [
                    'context' => 'blocks/txt/context.txt.twig',
                ],
            ],
            'event' => [
                'blocks' => [
                    'first' => ['template' => 'blocks/html/first.html.twig', 'priority' => 5],
                    'context' => ['template' => 'blocks/html/context.html.twig', 'priority' => -5],
                ],
            ],
            'context_template_block' => [
                'blocks' => [
                    'block' => [
                        'template' => 'blocks/contextTemplateBlock/block.txt.twig',
                        'context' => [
                            'option1' => 'foo',
                            'option2' => 'bar',
                        ],
                    ],
                ],
            ],
            'multiple_events_generic' => [
                'blocks' => [
                    'first' => [
                        'template' => 'blocks/multipleEvents/genericFirst.txt.twig',
                    ],
                    'second' => [
                        'template' => 'blocks/multipleEvents/genericSecond.txt.twig',
                        'context' => ['value' => 13],
                    ],
                ],
            ],
            'multiple_events_specific' => [
                'blocks' => [
                    'specific' => [
                        'template' => 'blocks/multipleEvents/specific.txt.twig',
                        'priority' => 3,
                    ],
                    'second' => [
                        'context' => ['value' => 42],
                        'priority' => 5,
                    ],
                ],
            ],
        ]]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }
}
