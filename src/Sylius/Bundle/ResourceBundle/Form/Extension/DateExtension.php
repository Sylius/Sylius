<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ('single_text' === $options['widget']) {
            $format = $this->getDatepickerPattern($options['format'], $options['leading_zero']);

            $view->vars['attr']['placeholder'] = null !== $options['placeholder'] ? $options['placeholder'] : $format;
            $view->vars['attr']['data-provide'] = 'datepicker-inline';
            $view->vars['attr']['data-date-language'] = $options['language'];
            $view->vars['attr']['data-date-format'] = $format;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $format = function (Options $options, $value) {
            if ($options['widget'] === 'single_text') {
                $formatter = new \IntlDateFormatter(
                    \Locale::getDefault(),
                    \IntlDateFormatter::SHORT,
                    \IntlDateFormatter::NONE
                );

                $format = $formatter->getPattern();
                $this->replaceInString('yy', 'yyyy', $format);
                $this->replaceInString('d', 'dd', $format);

                return $format;
            }

            return $value;
        };

        $resolver->setDefaults(array(
            'format' => $format,
            'language' => \Locale::getDefault(),
            'leading_zero' => false,
        ));

        $resolver->setDefined(array(
            'placeholder',
            'language',
            'leading_zero',
        ));

        $resolver->setAllowedTypes('placeholder', 'string');
        $resolver->setAllowedTypes('language', 'string');
        $resolver->setAllowedTypes('leading_zero', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'date';
    }

    /**
     * Get the datepicker date pattern
     *
     * @param string  $formPattern
     * @param boolean $leadingZero
     *
     * @return string
     */
    private function getDatepickerPattern($formPattern, $leadingZero)
    {
        if ($leadingZero) {
            $this->replaceInString('d', 'dd', $formPattern);
        }

        $this->replaceInString('y', 'yyyy', $formPattern);
        $this->replaceInString('M', $leadingZero ? 'mm' : 'm', $formPattern);
        $this->replaceInString('MM', 'mm', $formPattern);
        $this->replaceInString('MMM', 'M', $formPattern);
        $this->replaceInString('MMMM', 'MM', $formPattern);

        return $formPattern;
    }

    /**
     * Replace the search string with the replacement string
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     */
    private function replaceInString($search, $replace, &$subject)
    {
        if (1 === substr_count(strtolower($subject), strtolower($search))) {
            $subject = str_ireplace($search, $replace, $subject);
        }
    }
}
