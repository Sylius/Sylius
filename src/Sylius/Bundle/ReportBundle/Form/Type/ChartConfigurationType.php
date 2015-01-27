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
                    'bar' => 'Bar chart',
                    'line' => 'Line chart',
                    'radar' => 'Radar chart',
                    'polar' => 'Polar chart',
                    'pie' => 'Pie chart',
                    'doughnut' => 'Doughnut chart',
                ),
            ))
            ->add('template', 'choice', array(
                'label' => 'sylius.form.report.renderer.template',
                'choices' => array(
                    'SyliusReportBundle:Chart:default.html.twig' => 'Default',
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_renderer_chart';
    }
}
