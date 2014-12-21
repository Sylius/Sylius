<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Aram Alipoor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Form\Type;

use Sylius\Bundle\LocaleBundle\Form\DataTransformer\ArrayGregorianToCalendarSystemTransformer;
use Sylius\Bundle\LocaleBundle\Form\DataTransformer\StringGregorianToCalendarSystemTransformer;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;
use Symfony\Component\Form\Extension\Core\Type\DateType as BaseDateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class DateType
 *
 * Calendar-aware date form type
 */
class DateType extends BaseDateType {

    /**
     * @var LocaleHelper
     */
    protected $localeHelper;

    /**
     * @param LocaleHelper $helper
     */
    public function __construct(LocaleHelper $helper)
    {
        $this->localeHelper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($this->localeHelper->getCalendar() === 'gregorian') {
            // We only need to alter default behaviour when
            // we're having a traditional calendar system.
            return;
        }

        if ('single_text' === $options['widget']) {
            $builder->addViewTransformer(new StringGregorianToCalendarSystemTransformer(
                'Y-m-d',
                $this->localeHelper
            ));

        } elseif ('array' === $options['input']) {
            $builder->addViewTransformer(new ArrayGregorianToCalendarSystemTransformer(
                $this->localeHelper
            ));
        }

        switch ($options['widget']) {

            case 'choice':

                $this->translateChoices($builder, 'year', 'Y');
                $this->translateChoices($builder, 'month', 'm');
                $this->translateChoices($builder, 'day', 'd');

                break;
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ('single_text' === $options['widget']) {
            // Native browsers do not support different calendar systems
            if ($this->localeHelper->getCalendar() !== 'gregorian') {
                // We only need to alter default behaviour when
                // we're having a traditional calendar system.
                $options['html5'] = false;
            }
        }

        parent::finishView($view, $form, $options);
    }

    private function translateChoices(FormBuilderInterface $builder, $fieldName, $pattern)
    {
        $field   = $builder->get($fieldName);
        $options = $field->getOptions();
        $type    = $field->getType()->getName();

        if (isset($options['choices'])) {
            // Translate values
            $newValues = array();
            $values = $options['choices'];

            foreach ($values as $value => $presentation) {
                $newVal = $this->localeHelper->formatDate(\DateTime::createFromFormat($pattern, $value), $pattern);
                $newValues[$newVal] = $newVal;
            }

            $options['choices'] = $newValues;
        }

        unset($options['choice_list']);

        // Replace the field
        $builder->add($fieldName, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
} 