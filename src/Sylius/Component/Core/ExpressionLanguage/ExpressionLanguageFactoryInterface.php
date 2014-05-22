<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Expression language factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ExpressionLanguageFactoryInterface
{
    /**
     * Return new expression language instance.
     *
     * @return ExpressionLanguage
     */
    public function create();
}
