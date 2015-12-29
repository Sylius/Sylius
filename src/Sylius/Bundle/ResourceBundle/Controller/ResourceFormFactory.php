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
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceFormFactory implements ResourceFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        if ($requestConfiguration->isHtmlRequest()) {
            return $this->formFactory->create($requestConfiguration->getFormType(), $resource);
        }

        return $this->formFactory->createNamed('', $requestConfiguration->getFormType(), $resource, array('csrf_protection' => false));
    }
}
