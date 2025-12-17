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

namespace Sylius\Bundle\CoreBundle\OrderPay\Processor;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/** @experimental */
final class RouteParametersProcessor implements RouteParametersProcessorInterface
{
    public function __construct(
        private ExpressionLanguage $expressionLanguage,
        private RouterInterface $router,
    ) {
    }

    public function process(
        string $route,
        array $rawParameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
        array $context = [],
    ): string {
        $parameters = [];
        foreach ($rawParameters as $key => $rawParameter) {
            $parameters[$key] = (string) $this->expressionLanguage->evaluate($rawParameter, $context);
        }

        return $this->router->generate($route, $parameters, $referenceType);
    }
}
