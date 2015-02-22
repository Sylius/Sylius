<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\ColumnType;

use Sylius\Component\Grid\Definition\Grid;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TwigColumnType extends AbstractColumnType
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, $name, array $options = array())
    {
        return $this->twig->render($options['template'], array_merge(array('data' => $data), $options['context']));
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'template'
            ))
            ->setDefaults(array(
                'context' => array()
            ))
            ->setAllowedTypes(array(
                'template' => array('string'),
                'context'  => array('array')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'twig';
    }
}
