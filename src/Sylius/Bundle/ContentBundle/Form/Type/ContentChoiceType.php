<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentChoiceType extends AbstractType
{
    /**
     * @var string
     */
    private $contentModel;

    public function __construct($contentModel)
    {
        $this->contentModel = $contentModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => $this->contentModel,
            'property' => 'title',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'phpcr_document';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_content_choice';
    }
}
