<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SelectAttributeChoicesCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (null !== $data) {
                $fixedArray = [];
                foreach ($data as $key => $value) {
                    if (!is_int($key)) {
                        $fixedArray[$key] = $value;

                        continue;
                    }

                    $newKey = $this->getUniqueKey();
                    $fixedArray[$newKey] = $value;

                    if ($form->offsetExists($key)) {
                        $form->offsetUnset($key);
                        $form->offsetSet(null, $newKey);
                    }
                }

                $event->setData($fixedArray);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_select_attribute_choices_collection';
    }

    /**
     * @return string
     */
    private function getUniqueKey(): string
    {
        return Uuid::uuid1()->toString();
    }
}
