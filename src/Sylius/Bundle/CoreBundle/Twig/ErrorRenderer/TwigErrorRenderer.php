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

use Sylius\Bundle\CoreBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer as DecoratedTwigErrorRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Twig\Environment;

class TwigErrorRenderer implements ErrorRendererInterface
{
    /**
     * @param iterable<ErrorTemplateFinderInterface> $templateFinders
     */
    public function __construct(
        private DecoratedTwigErrorRenderer $decoratedTwigErrorRenderer,
        private Environment $twig,
        private iterable $templateFinders,
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
        foreach ($this->templateFinders as $templateFinder) {
            if (null !== $template = $templateFinder->findTemplate($statusCode)) {
                return $template;
            }
        }

        return null;
    }
}
