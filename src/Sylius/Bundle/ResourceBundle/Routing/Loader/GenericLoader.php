<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing\Loader;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class GenericLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    protected function createResourceRoutes($resource)
    {
        $this->collectionBuilder->add($resource, 'index', array('GET'));
        $this->collectionBuilder->add($resource, 'show', array('GET'));
        $this->collectionBuilder->add($resource, 'create', array('POST', 'GET'));
        $this->collectionBuilder->add($resource, 'update', array('POST', 'GET'));
        $this->collectionBuilder->add($resource, 'delete', array('DELETE'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedType()
    {
        return 'sylius';
    }
}
