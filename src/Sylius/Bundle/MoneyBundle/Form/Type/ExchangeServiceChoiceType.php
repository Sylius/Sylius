<?php
namespace Sylius\Bundle\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ExchangeServiceChoiceType
 *
 * Choice for Exchange Rate providers
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ExchangeServiceChoiceType extends AbstractType
{
    /**
     * Exchange Rate class name.
     *
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'class' => $this->className
            ))
        ;
    }

    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_exchange_service_choice';
    }
}
