<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValue implements OptionValueInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationCollection;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var OptionInterface
     */
    protected $option;

    public function __construct()
    {
        $this->initializeTranslationCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(OptionInterface $option = null)
    {
        $this->option = $option;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->translate()->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->translate()->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionCode()
    {
        if (null === $this->option) {
            throw new \BadMethodCallException('The option have not been created yet so you cannot access proxy methods.');
        }

        return $this->option->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getPresentation()
    {
        if (null === $this->option) {
            throw new \BadMethodCallException('The option have not been created yet so you cannot access proxy methods.');
        }

        return $this->option->getName();
    }
}
