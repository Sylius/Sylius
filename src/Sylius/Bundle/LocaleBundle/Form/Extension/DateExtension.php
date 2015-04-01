<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Form\Extension;

use Sylius\Bundle\LocaleBundle\Form\DataTransformer\ArrayGregorianToCalendarSystemTransformer;
use Sylius\Bundle\LocaleBundle\Form\DataTransformer\StringGregorianToCalendarSystemTransformer;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractTypeExtension;

/**
 * Class DateExtension
 *
 * Extension for DateType to add calendar-aware date capabilities
 */
class DateExtension extends AbstractTypeExtension {

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
        if ($this->localeHelper->getCalendar() === 'gregorian') {
            // We only need to alter default behaviour when
            // we're having a traditional calendar system.
            return;
        }

        if ('single_text' === $options['widget']) {
            // When date is in string format
            $builder->addViewTransformer(new StringGregorianToCalendarSystemTransformer(
                'Y-m-d',
                $this->localeHelper
            ));

        } elseif ('array' === $options['input']) {
            // When date is in array (of `year`, `month`, `day`) format
            $builder->addViewTransformer(new ArrayGregorianToCalendarSystemTransformer(
                $this->localeHelper
            ));
        }

        switch ($options['widget']) {

            // If widget is type of `choice` so we need to translate choice items
            // stored in `choices` option of the field to their localized version.
            case 'choice':

                $this->translateChoices($builder, 'year', 'Y');
                $this->translateChoices($builder, 'month', 'm');
                $this->translateChoices($builder, 'day', 'd');

                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ('single_text' === $options['widget']) {
            // Native browsers do not support different calendar systems
            // so we have to use "text" type instead of "date" for the input.
            if ($this->localeHelper->getCalendar() !== 'gregorian') {
                // We only need to alter default behaviour when
                // we're having a traditional calendar system.
                $view->vars['type'] = 'text';
            }
        }
    }

    /**
     * A helper method to translate choices of a date-related field
     * to their localized version.
     *
     * e.g. For a year field it should
     *      translate 2014 (gregorian) choice to to 1393 (persian)
     *
     * @param FormBuilderInterface $builder Form builder instance
     * @param string $fieldName Field to translate its choices
     * @param string $format PHP-compatible date format to read choices as!
     */
    private function translateChoices(FormBuilderInterface $builder, $fieldName, $format)
    {
        $field   = $builder->get($fieldName);

        $options = $field->getOptions();
        $type    = $field->getType()->getName();

        if (isset($options['choices'])) {
            $newValues = array();
            $values = $options['choices'];

            // Translate values
            foreach ($values as $value => $presentation) {
                $newVal = $this->localeHelper->formatDate(\DateTime::createFromFormat($format, $value), $format);
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
    public function getExtendedType()
    {
        return 'date';
    }
} 