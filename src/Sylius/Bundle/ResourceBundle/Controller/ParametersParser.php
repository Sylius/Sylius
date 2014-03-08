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
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Configuration parameters parser.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ParametersParser
{
    /**
     * @var ExpressionLanguage
     */
    private $expression;

    public function __construct(ExpressionLanguage $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @param array   $parameters
     * @param Request $request
     *
     * @return array
     */
    public function parse(array &$parameters, Request $request)
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->parse($value, $request);
            }

            if (is_string($value) && 0 === strpos($value, '$')) {
                $parameters[$key] = $request->get(substr($value, 1));
            }

            if (is_string($value) && 0 === strpos($value, 'expr:')) {
                $parameters[$key] = $this->expression->evaluate(substr($value, 5));
            }
        }

        return $parameters;
    }

    /**
     * @param array  $parameters
     * @param object $resource
     *
     * @return array
     */
    public function process(array &$parameters, $resource)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        if (empty($parameters)) {
            return array('id' => $accessor->getValue($resource, 'id'));
        }

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->process($value, $resource);
            }

            if (is_string($value) && 0 === strpos($value, 'resource.')) {
                $parameters[$key] = $accessor->getValue($resource, substr($value, 9));
            }
        }

        return $parameters;
    }
}
