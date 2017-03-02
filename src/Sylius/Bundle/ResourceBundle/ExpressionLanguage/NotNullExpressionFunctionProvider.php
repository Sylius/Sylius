<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class NotNullExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction('notFoundOnNull', function ($result) {
                return sprintf('(null !== %1$s) ? %1$s : throw new NotFoundHttpException(\'Requested page is invalid.\')', $result);
            }, function ($arguments, $result) {
                if (null === $result) {
                    throw new NotFoundHttpException('Requested page is invalid.');
                }

                return $result;
            }),
        ];
    }
}
