<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\Type;

use Sylius\Component\Variation\Model\OptionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValueChoiceType extends AbstractType
{
    /**
     * @var string
     */
    protected $variableName;

    /**
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceList = function (Options $options) {
            return new ObjectChoiceList(
                $options['option']->getValues(),
                'value',
                [],
                null,
                'id',
                PropertyAccess::createPropertyAccessor()
            );
        };

        $resolver
            ->setDefaults([
                'choice_translation_domain' => false,
                'choice_list' => $choiceList,
            ])
            ->setRequired([
                'option',
            ])
            ->addAllowedTypes([
                'option' => OptionInterface::class,
            ])
        ;
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
        return sprintf('sylius_%s_option_value_choice', $this->variableName);
    }
}
