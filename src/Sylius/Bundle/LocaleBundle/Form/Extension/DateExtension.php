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
use Sylius\Component\Locale\Calendars;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Extension for Symfony's DateType for a localized calendar-aware date.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class DateExtension extends AbstractTypeExtension
{
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
        if (Calendars::GREGORIAN === $this->localeHelper->getCurrentCalendar()) {
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

        if ('choice' === $options['widget']) {
            $this->translateChoices($builder, 'year', 'Y');
            $this->translateChoices($builder, 'month', 'm');
            $this->translateChoices($builder, 'day', 'd');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ('single_text' === $options['widget'] &&
            Calendars::GREGORIAN !== $this->localeHelper->getCurrentCalendar()) {
            // Native browsers do not support different calendar systems
            // so we have to use "text" type instead of "date" for the input.
            // We only need to alter default behaviour when
            // we're having a traditional calendar system.
            $view->vars['type'] = 'text';
        }
    }

    /**
     * A helper method to translate choices of a date-related field
     * to their localized version.
     *
     * e.g. For a year field it should translate 2014 (gregorian) choice to to 1393 (persian)
     *
     * @param FormBuilderInterface $builder
     * @param string               $fieldName
     * @param string               $format    PHP-compatible date format
     */
    private function translateChoices(FormBuilderInterface $builder, $fieldName, $format)
    {
        $field = $builder->get($fieldName);
        $options = $field->getOptions();
        $type = $field->getType()->getName();

        if (isset($options['choices'])) {
            $newValues = [];
            $values = $options['choices'];

            foreach ($values as $presentation => $value) {
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
