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

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface ResourceFormFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return FormInterface
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceInterface $resource);
}
