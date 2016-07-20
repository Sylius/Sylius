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

use Sylius\Bundle\ResourceBundle\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Dosena Ishmael <nukboon@gmail.com>
 */
class ParametersParser implements ParametersParserInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expression;

    /**
     * @param ExpressionLanguage $expression
     */
    public function __construct(ExpressionLanguage $expression)
    {
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

                $parameters[$key] = $this->expression->evaluate($service);
            }
        }

        return $parameters;
    }
}
