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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Dosena Ishmael <nukboon@gmail.com>
 */
final class ParametersParser implements ParametersParserInterface
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
     * @param ContainerInterface $container
     * @param ExpressionLanguage $expression
     */
    public function __construct(ContainerInterface $container, ExpressionLanguage $expression)
    {
        $this->container = $container;
        $this->expression = $expression;
    }

    /**
     * {@inheritdoc}
     */
    public function parseRequestValues(array $parameters, Request $request)
    {
        return array_map(function ($parameter) use ($request) {
            if (is_array($parameter)) {
                return $this->parseRequestValues($parameter, $request);
            }

            return $this->parseRequestValue($parameter, $request);
        }, $parameters);
    }

    /**
     * @param mixed $parameter
     * @param Request $request
     *
     * @return mixed
     */
    private function parseRequestValue($parameter, Request $request)
    {
        if (!is_string($parameter)) {
            return $parameter;
        }

        if (0 === strpos($parameter, '$')) {
            return $request->get(substr($parameter, 1));
        }

        if (0 === strpos($parameter, 'expr:')) {
            return $this->parseRequestValueExpression(substr($parameter, 5), $request);
        }

        return $parameter;
    }

    /**
     * @param string $expression
     * @param Request $request
     *
     * @return string
     */
    private function parseRequestValueExpression($expression, Request $request)
    {
        $expression = preg_replace_callback('/(\$\w+)/', function ($matches) use ($request) {
            $variable = $request->get(substr($matches[1], 1));

            if (is_array($variable) || is_object($variable)) {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot use %s ($%s) as parameter in expression.',
                    gettype($variable),
                    $matches[1]
                ));
            }

            return is_string($variable) ? sprintf('"%s"', $variable) : $variable;
        }, $expression);

        return $this->expression->evaluate($expression, ['container' => $this->container]);
    }
}
