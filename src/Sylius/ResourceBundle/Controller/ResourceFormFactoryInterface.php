<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ResourceBundle\Controller;

use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
