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

namespace Sylius\Bundle\ResourceBundle\Grid\Parser;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class OptionsParser implements OptionsParserInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var ExpressionLanguage */
    private $expression;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(
        ContainerInterface $container,
        ExpressionLanguage $expression,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->container = $container;
        $this->expression = $expression;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function parseOptions(array $parameters, Request $request, $data = null): array
    {
        return array_map(function ($parameter) use ($request, $data) {
            if (is_array($parameter)) {
                return $this->parseOptions($parameter, $request, $data);
            }

            return $this->parseOption($parameter, $request, $data);
        }, $parameters);
    }

    private function parseOption($parameter, Request $request, $data)
    {
        if (!is_string($parameter)) {
            return $parameter;
        }

        if (0 === strpos($parameter, '$')) {
            return $request->get(substr($parameter, 1));
        }

        if (0 === strpos($parameter, 'expr:')) {
            return $this->parseOptionExpression(substr($parameter, 5), $request);
        }

        if (0 === strpos($parameter, 'resource.')) {
            return $this->parseOptionResourceField(substr($parameter, 9), $data);
        }

        return $parameter;
    }

    private function parseOptionExpression(string $expression, Request $request)
    {
        $expression = preg_replace_callback('/\$(\w+)/', function (array $matches) use ($request) {
            $variable = $request->get($matches[1]);

            return is_string($variable) ? sprintf('"%s"', $variable) : $variable;
        }, $expression);

        return $this->expression->evaluate($expression, ['container' => $this->container]);
    }

    private function parseOptionResourceField(string $value, $data)
    {
        return $this->propertyAccessor->getValue($data, $value);
    }
}
