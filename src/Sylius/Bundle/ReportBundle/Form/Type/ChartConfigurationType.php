<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Renderer configuration form type
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChartConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'label' => 'sylius.form.report.chart.type',
                'choices' => array(
                    0 => 'Bar chart',
                    1 => 'Line chart'
                ),
            ))
            ->add('template', 'choice', array(
                'label' => 'sylius.form.report.renderer.template',
                'choices' => array(
                    0 => 'Template 0',
                    1 => 'Template 1'
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_report_renderer_chart_configuration';
    }
}