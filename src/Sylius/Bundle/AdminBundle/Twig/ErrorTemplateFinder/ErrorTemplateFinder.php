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

use Sylius\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Twig\Environment;

final readonly class ErrorTemplateFinder implements ErrorTemplateFinderInterface
{
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private LoggedInAdminUserProviderInterface $loggedInAdminUserProvider,
        private Environment $twig,
    ) {
    }

    public function findTemplate(int $statusCode): ?string
    {
        $section = $this->sectionProvider->getSection();

        if ($section instanceof AdminSection && $this->loggedInAdminUserProvider->hasUser()) {
            $template = sprintf('@SyliusAdmin/errors/error%s.html.twig', $statusCode);
            if ($this->twig->getLoader()->exists($template)) {
                return $template;
            }

            $template = '@SyliusAdmin/errors/error.html.twig';
            if ($this->twig->getLoader()->exists($template)) {
                return $template;
            }
        }

        return null;
    }
}
