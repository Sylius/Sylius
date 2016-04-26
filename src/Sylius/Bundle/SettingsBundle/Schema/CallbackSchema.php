<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @todo Remove and replace with anonymous classes after bump to PHP 7
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CallbackSchema implements SchemaInterface
{
    /**
     * @var callable
     */
    private $buildSettings;

    /**
     * @var callable
     */
    private $buildForm;

    /**
     * @see SchemaInterface
     *
     * @param callable $buildSettings Receives the same arguments as SchemaInterface::buildSettings method
     * @param callable $buildForm Receives the same arguments as SchemaInterface::buildForm method
     */
    public function __construct(callable $buildSettings, callable $buildForm)
    {
        $this->buildSettings = $buildSettings;
        $this->buildForm = $buildForm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        // Workaround for PHP 5
        $buildSettings = $this->buildSettings;

        $buildSettings($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        // Workaround for PHP 5
        $buildForm = $this->buildForm;

        $buildForm($builder);
    }
}
