<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Exception\Driver\UnknownDriverException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extending Doctrine document/entity/phpcr_document choice form types.
 *
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 */
class ResourceChoiceType extends AbstractType
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $parent;

    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    /**
     * @param string $className
     * @param string $driver
     * @param string $name
     *
     * @throws UnknownDriverException
     */
    public function __construct($className, $driver, $name)
    {
        $this->className = $className;
        $this->name = $name;
        $this->parent = $this->getFormTypeForDriver($driver);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $className = $this->className;
        $resolver
            ->setDefaults(array(
                'class' => null,
            ))
            ->setNormalizers(array(
                'class' => function () use ($className) {
                    return $className;
                },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $driver
     *
     * @return string
     *
     * @throws UnknownDriverException
     */
    protected function getFormTypeForDriver($driver)
    {
        switch ($driver) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return 'document';
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return 'entity';
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return 'phpcr_document';
        }
        throw new UnknownDriverException($driver);
    }
}
