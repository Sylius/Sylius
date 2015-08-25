<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Factory;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;

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
     * @var DefaultResourceFormFactoryInterface
     */
    private $defaultResourceFormFactory;

    /**
     * @var FormRegistryInterface
     */
    private $formRegistry;

    /**
     * @param FormFactoryInterface                $formFactory
     * @param DefaultResourceFormFactoryInterface $defaultResourceFormFactory
     * @param FormRegistryInterface               $formRegistry
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        DefaultResourceFormFactoryInterface $defaultResourceFormFactory,
        FormRegistryInterface $formRegistry
    ) {
        $this->formFactory = $formFactory;
        $this->defaultResourceFormFactory = $defaultResourceFormFactory;
        $this->formRegistry = $formRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(RequestConfiguration $configuration, ResourceMetadataInterface $metadata)
    {
        $type = $configuration->getFormType();

        if (strpos($type, '\\') !== false) { // Full class name specified?
            $type = new $type();
        } elseif (!$this->formRegistry->hasType($type)) {
            return $this->defaultResourceFormFactory->create($configuration, $metadata);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->formFactory->createNamed('', $type, null, array('csrf_protection' => false));
        }

        return $this->formFactory->create($type, null);
    }
}
