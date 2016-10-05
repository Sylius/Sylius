<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->parseRequestValues($value, $request);
            }

            if (is_string($value) && 0 === strpos($value, '$')) {
                $parameterName = substr($value, 1);
                $parameters[$key] = $request->get($parameterName);
            }

            if (is_string($value) && 0 === strpos($value, 'expr:')) {
                $service = substr($value, 5);

                if (preg_match_all('/(\$\w+)\W/', $service, $match)) {
                    foreach ($match[1] as $parameterName) {
                        $parameter = $request->get(substr(trim($parameterName), 1));
                        $parameter = is_string($parameter) ? sprintf('"%s"', $parameter) : $parameter;
                        $service = str_replace($parameterName, $parameter, $service);
                    }
                }

                $parameters[$key] = $this->expression->evaluate($service, ['container' => $this->container]);
            }
        }

        return $parameters;
    }
}
