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

namespace Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder;

use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\CoreBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class ErrorTemplateFinder implements ErrorTemplateFinderInterface
{
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private RequestStack $requestStack,
        private Environment $twig,
    ) {
    }

    public function findTemplate(int $statusCode): ?string
    {
        $section = $this->sectionProvider->getSection();

        if ($section instanceof AdminSection && $this->isLoggedInAdmin()) {
            $template = sprintf('@Twig/Exception/Admin/error%s.html.twig', $statusCode);
            if ($this->twig->getLoader()->exists($template)) {
                return $template;
            }

            $template = '@Twig/Exception/Admin/error.html.twig';
            if ($this->twig->getLoader()->exists($template)) {
                return $template;
            }
        }

        return null;
    }

    private function isLoggedInAdmin(): bool
    {
        return !empty($this->requestStack->getSession()->get('_security_admin'));
    }
}
