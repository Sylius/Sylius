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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Report\Model\ReportInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Report form type.
 */
class ReportType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_report';
    }
}
