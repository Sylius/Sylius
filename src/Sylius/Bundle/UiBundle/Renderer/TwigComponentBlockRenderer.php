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

namespace Sylius\Bundle\UiBundle\Renderer;

use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\Block;
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\UX\TwigComponent\ComponentRendererInterface;

/** @internal */
final class TwigComponentBlockRenderer implements SupportableBlockRendererInterface
{
    public function __construct(
        private ComponentRendererInterface $componentRenderer,
        private ContextProviderInterface $contextProvider,
        private ExpressionLanguage $expressionLanguage,
    ) {
    }

    /**
     * @param ComponentBlock $templateBlock
     * @param array<string, mixed> $context
     */
    public function render(Block $templateBlock, array $context = []): string
    {
        if (!$this->supports($templateBlock)) {
            throw new \InvalidArgumentException(sprintf(
                'Block "%s" is not supported by "%s".',
                $templateBlock->getName(),
                self::class,
            ));
        }

        $context = $this->contextProvider->provide($context, $templateBlock);

        $inputs = $this->mapArrayRecursively(function (mixed $value) use ($context) {
            if (!is_string($value)) {
                return $value;
            }

            if (!str_starts_with($value, 'expr:')) {
                return $value;
            }

            return $this->expressionLanguage->evaluate(substr($value, 5), ['context' => $context]);
        }, $templateBlock->getComponentInputs());

        return $this->componentRenderer->createAndRender($templateBlock->getComponentName(), $inputs);
    }

    /**
     * @param array<array-key, mixed> $array
     *
     * @return array<array-key, mixed>
     */
    private function mapArrayRecursively(callable $callback, array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = is_array($value)
                ? $this->mapArrayRecursively($callback, $value)
                : $callback($value);
        }

        return $result;
    }

    public function supports(Block $block): bool
    {
        return is_a($block, ComponentBlock::class);
    }
}
