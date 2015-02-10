<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form choice type for email templates.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EmailTemplateChoiceType extends AbstractType
{
    /**
     * @var array
     */
    protected $templates;

    /**
     * @param array $templates
     */
    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array_flip($this->templates)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_email_template_choice';
    }
}
