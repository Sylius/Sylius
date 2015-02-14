<?php

namespace Sylius\Bundle\ResourceBundle\Form\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Form\Guesser\FieldGuesser;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;

class FormFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormRegistryInterface
     */
    private $formRegistry;

    /**
     * @var FieldGuesser
     */
    private $fieldGuesser;

    public function __construct(
        FormFactoryInterface $formFactory,
        FormRegistryInterface $formRegistry,
        FieldGuesser $fieldGuesser
    ) {
        $this->formFactory = $formFactory;
        $this->formRegistry = $formRegistry;
        $this->fieldGuesser = $fieldGuesser;
    }

    /**
     * @param string $type
     * @param object $resource
     * @param array $options
     *
     * @return FormInterface
     */
    public function create($type, $resource, $name = 'form', array $options = array())
    {
        if (class_exists($type)) {
            $type = new $type();
        } elseif (!$this->formRegistry->hasType($type)) {
            // TODO
            $builder = $this->formFactory->createNamedBuilder($type, 'form', $resource, $options);
            $fields = $this->fieldGuesser->guess($resource);

            foreach ($fields as $field => $options) {
                $builder->add($field, null, $options);
            }

            return $builder->getForm();
        }

        return $this->formFactory->create($type, $resource, $options);
    }
}
