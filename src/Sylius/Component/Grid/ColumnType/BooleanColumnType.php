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

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BooleanColumnType extends TwigColumnType
{
    /**
     * @var string
     */
    private $defaultTemplate;

    /**
     * @param \Twig_Environment $twig
     * @param string            $defaultTemplate
     */
    public function __construct(\Twig_Environment $twig, $defaultTemplate)
    {
        parent::__construct($twig);

        $this->defaultTemplate = $defaultTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, $name, array $options = array())
    {
        $value = $this->getPropertyAccessor()->getValue($data, isset($options['path']) ? $options['path'] : $name);

        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Expected boolean value, got "%s".', gettype($value)));
        }

        return $this->twig->render($options['template'], array('value' => $value));
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'template' => $this->defaultTemplate
            ))
            ->setAllowedTypes(array(
                'template' => array('string')
            ))
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'boolean';
    }
}
