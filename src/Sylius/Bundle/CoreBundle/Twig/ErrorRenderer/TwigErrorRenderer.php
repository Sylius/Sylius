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

namespace Sylius\Bundle\CoreBundle\Twig\ErrorRenderer;

use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer as DecoratedTwigErrorRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Twig\Environment;

class TwigErrorRenderer implements ErrorRendererInterface
{
    public function __construct(
        private Environment $twig,
        private DecoratedTwigErrorRenderer $decoratedTwigErrorRenderer,
        private SectionProviderInterface $sectionProvider,
        private bool $debug,
    ) {
    }

    public function render(\Throwable $exception): FlattenException
    {
        $flattenException = FlattenException::createFromThrowable($exception);

        if ($this->debug || !$template = $this->findTemplate($flattenException->getStatusCode())) {
            return $this->decoratedTwigErrorRenderer->render($exception);
        }

        return $flattenException->setAsString($this->twig->render($template, [
            'exception' => $flattenException,
            'status_code' => $flattenException->getStatusCode(),
            'status_text' => $flattenException->getStatusText(),
        ]));
    }

    private function findTemplate(int $statusCode): ?string
    {
        $section = $this->sectionProvider->getSection();

        $template = ($section instanceof AdminSection) ?
            sprintf('@Twig/Exception/Admin/error%s.html.twig', $statusCode) :
            sprintf('@Twig/Exception/Shop/error%s.html.twig', $statusCode);

        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }

        $template = ($section instanceof AdminSection) ?
            '@Twig/Exception/Admin/error.html.twig' :
            '@Twig/Exception/Shop/error.html.twig';

        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }

        return null;
    }
}
