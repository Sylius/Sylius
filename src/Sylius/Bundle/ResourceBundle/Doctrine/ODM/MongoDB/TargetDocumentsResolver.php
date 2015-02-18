<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB;

use Sylius\Bundle\ResourceBundle\Doctrine\Common\AbstractTargetResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class TargetDocumentsResolver extends AbstractTargetResolver
{
    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return 'doctrine_mongodb.odm.event_listener';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMethodName()
    {
        return 'addResolveTargetDocument';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTargetResolverService()
    {
        return 'doctrine_mongodb.odm.listeners.resolve_target_document';
    }
}
