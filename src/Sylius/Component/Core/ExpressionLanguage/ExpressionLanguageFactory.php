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
class ExpressionLanguageFactory implements ExpressionLanguageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new ExpressionLanguage();
    }
}
