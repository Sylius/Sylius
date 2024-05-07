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

namespace Sylius\Bundle\UiBundle\Twig\Ux;

use Symfony\UX\TwigComponent\ComponentTemplateFinderInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

final readonly class ComponentTemplateFinder implements ComponentTemplateFinderInterface
{
    public function __construct(
        private ComponentTemplateFinderInterface $decorated,
        private Environment|LoaderInterface $loader,
    ) {
    }

    public function findAnonymousComponentTemplate(string $name): ?string
    {
        if (!str_starts_with($name, 'sylius_ui')) {
            return $this->decorated->findAnonymousComponentTemplate($name);
        }

        $template = $this->guessTemplatePath($name);

        if ($this->loader->exists($template)) {
            return $template;
        }

        return null;
    }

    private function guessTemplatePath(string $name): string
    {
        $normalizedName = str_replace('sylius_ui:', '', $name);
        $normalizedName = str_replace(':', '/', $normalizedName);

        return sprintf('@SyliusUi/component/%s.html.twig', $normalizedName);
    }
}
