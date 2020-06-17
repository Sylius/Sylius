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

namespace Sylius\Bundle\UiBundle\Renderer;

use Sylius\Bundle\ResourceBundle\ExpressionLanguage\ExpressionLanguage;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Symfony\Component\ExpressionLanguage\Expression;
use Twig\Environment;

/**
 * @experimental
 */
final class TwigTemplateBlockRenderer implements TemplateBlockRendererInterface
{
    /** @var Environment */
    private $twig;

    /** @var ExpressionLanguage */
    private $expressionLanguage;

    public function __construct(
        Environment $twig,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->twig = $twig;
        $this->expressionLanguage = $expressionLanguage;
    }

    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        $context = array_replace($templateBlock->getContext(), $context);
        foreach ($context as $key => &$value) {
            if ($value instanceof Expression) {
                $value = $this->expressionLanguage->evaluate($value);
            }
        }

        return $this->twig->render(
            $templateBlock->getTemplate(),
            $context
        );
    }
}
