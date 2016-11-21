<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Grid\Parser;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OptionsParser implements OptionsParserInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ExpressionLanguage
     */
    private $expression;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param ContainerInterface $container
     * @param ExpressionLanguage $expression
     * @param PropertyAccessorInterface $propertyAccessor
     */
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
    public function parseOptions(array $parameters, Request $request, $data = null)
    {
        return array_map(function ($parameter) use ($request, $data) {
            if (is_array($parameter)) {
                return $this->parseOptions($parameter, $request, $data);
            }

            return $this->parseOption($parameter, $request, $data);
        }, $parameters);
    }

    /**
     * @param mixed $parameter
     * @param Request $request
     * @param mixed $data
     *
     * @return mixed
     */
    private function parseOption($parameter, Request $request, $data)
    {
        if (0 === strpos($parameter, '$')) {
            return $request->get(substr($parameter, 1));
        }

        if (0 === strpos($parameter, 'expr:')) {
            return $this->parseOptionExpression(substr($parameter, 5), $request);
        }

        if (0 === strpos($parameter, 'resource.')) {
            return $this->parseOptionResourceField(substr($parameter, 9), $request, $data);
        }

        return $parameter;
    }

    /**
     * @param string $expression
     * @param Request $request
     *
     * @return string
     */
    private function parseOptionExpression($expression, Request $request)
    {
        $expression = preg_replace_callback('/(\$\w+)/', function ($matches) use ($request) {
            $variable = $request->get(substr($matches[1], 1));

            return is_string($variable) ? sprintf('"%s"', $variable) : $variable;
        }, $expression);

        return $this->expression->evaluate($expression, ['container' => $this->container]);
    }

    /**
     * @param string $value
     * @param Request $request
     * @param mixed $data
     *
     * @return string
     */
    private function parseOptionResourceField($value, Request $request, $data)
    {
        $value = preg_replace_callback('/(\$\w+)/', function ($matches) use ($request) {
            $variable = $request->get(substr($matches[1], 1));

            return is_string($variable) ? sprintf('"%s"', $variable) : $variable;
        }, $value);

        $value = 'get'.ucfirst($value);

        return $this->propertyAccessor->getValue($data, $value);
    }
}
