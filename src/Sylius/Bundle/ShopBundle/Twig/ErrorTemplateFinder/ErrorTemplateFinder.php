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

namespace Sylius\Bundle\ShopBundle\Twig\ErrorTemplateFinder;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Twig\Environment;

class ErrorTemplateFinder implements ErrorTemplateFinderInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider, private Environment $twig)
    {
    }

    public function findTemplate(int $statusCode): ?string
    {
        $section = $this->sectionProvider->getSection();

        if ($section instanceof ShopSection) {
            $template = sprintf('@Twig/Exception/Shop/error%s.html.twig', $statusCode);
            if ($this->twig->getLoader()->exists($template)) {
                return $template;
            }

            $template = '@Twig/Exception/Shop/error.html.twig';
            if ($this->twig->getLoader()->exists($template)) {
                return $template;
            }
        }

        return null;
    }
}
