<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\EventListener;

use Sylius\Component\Attribute\Model\AttributeTypes;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Form event listener that builds product property form dynamically based on product data.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Liverbool <liverbool@gmail.com>
 */
class BuildAttributeValueFormListener implements EventSubscriberInterface
{
    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Attributes subject name.
     *
     * @var string
     */
    protected $subjectName;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $factory
     * @param string               $subjectName
     */
    public function __construct(FormFactoryInterface $factory, $subjectName)
    {
        $this->factory = $factory;
        $this->subjectName = $subjectName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'buildForm');
    }

    /**
     * Builds proper product form after setting the product.
     *
     * @param FormEvent $event
     */
    public function buildForm(FormEvent $event)
    {
        $attributeValue = $event->getData();
        $form = $event->getForm();

        if (null === $attributeValue) {
            $form->add($this->factory->createNamed('value', 'text', null, array(
                'label' => sprintf('sylius.form.attribute.%s_attribute_value.value', $this->subjectName),
                'auto_initialize' => false,
            )));

            return;
        }

        $options = array('label' => $attributeValue->getName(), 'auto_initialize' => false);

        if (is_array($attributeValue->getConfiguration())) {
            $options = array_merge($options, $attributeValue->getConfiguration());
        }

        $this->verifyValue($attributeValue);

        // If we're editing the attribute value, let's just render the value field, not full selection.
        $form
            ->remove('attribute')
            ->add($this->factory->createNamed('value', $attributeValue->getType(), null, $options))
        ;
    }

    /**
     * Verify value before set to form.
     *
     * @param AttributeValueInterface $attributeValue
     */
    private function verifyValue(AttributeValueInterface $attributeValue)
    {
        switch ($attributeValue->getType()) {

            case AttributeTypes::CHECKBOX:
                if (!is_bool($attributeValue->getValue())) {
                    $attributeValue->setValue(false);
                }

                break;

            case AttributeTypes::MONEY:
            case AttributeTypes::NUMBER:
            case AttributeTypes::PERCENTAGE:
                if (!is_numeric($attributeValue->getValue())) {
                    $attributeValue->setValue(null);
                }

                break;
        }
    }
}
