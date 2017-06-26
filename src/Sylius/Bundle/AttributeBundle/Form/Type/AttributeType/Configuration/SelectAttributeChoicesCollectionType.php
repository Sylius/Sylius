<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class SelectAttributeChoicesCollectionType extends AbstractType
{
    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(TranslationLocaleProviderInterface $localeProvider)
    {
        $this->defaultLocaleCode = $localeProvider->getDefaultLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (null !== $data) {
                foreach ($data as $key => &$values) {
                    $newKey = null;
                    foreach ($values as $locale => &$value) {
                        if ($locale === $this->defaultLocaleCode) {
                            $newKey = $this->getValidFormKey($value);
                            $data[$newKey] = $values;
                            if ($key !== $newKey) {
                                unset($data[$key]);
                            }
                        }
                    }

                    if (!is_null($newKey) && $form->offsetExists($key)) {
                        $form->offsetUnset($key);
                        $form->offsetSet(null, $newKey);
                    }
                }

                $event->setData($data);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_select_attribute_choices_collection';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getValidFormKey($value)
    {
        $newKey = strtolower(str_replace(' ', '_', $value));
        $newKey = preg_replace('/[^a-zA-Z0-9\-_:]/', '', $newKey);
        $newKey = preg_replace('/^[^a-zA-Z0-9_]++/', '', $newKey);

        return $newKey;
    }
}
