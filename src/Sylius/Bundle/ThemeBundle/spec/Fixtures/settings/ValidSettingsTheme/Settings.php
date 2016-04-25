<?php

namespace Sturdy\Garbanzo;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class Settings implements SchemaInterface
{
    public function buildSettings(SettingsBuilderInterface $builder)
    {

    }

    public function buildForm(FormBuilderInterface $builder)
    {

    }
}

return new Settings();
