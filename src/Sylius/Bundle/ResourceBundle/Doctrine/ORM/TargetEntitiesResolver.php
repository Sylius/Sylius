<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\Common\AbstractTargetResolver;

/**
 * Resolves given target entities with container parameters.
 * Usable only with *doctrine/orm* driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class TargetEntitiesResolver extends AbstractTargetResolver
{
    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return 'doctrine.event_listener';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMethodName()
    {
        return 'addResolveTargetEntity';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTargetResolverService()
    {
        return 'doctrine.orm.listeners.resolve_target_entity';
    }
}
