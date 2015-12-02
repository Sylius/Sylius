<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\Type\Renderer;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sylius\Bundle\ReportBundle\Renderer\ChartRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Renderer configuration form type.
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
            ->add('type', ChoiceType::class, array(
                'label' => 'sylius.form.report.chart.type',
                'choices' => ChartRenderer::getChartTypes(),
            ))
            ->add('template', ChoiceType::class, array(
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
    public function getBlockPrefix()
    {
        return 'sylius_renderer_chart';
    }
}
