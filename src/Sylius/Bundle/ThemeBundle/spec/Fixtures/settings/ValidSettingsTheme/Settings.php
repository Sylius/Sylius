<?php

use Sylius\Bundle\SettingsBundle\Schema\CallbackSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

return new CallbackSchema(
    function (SettingsBuilderInterface $settingsBuilder) {

    },
    function (FormBuilderInterface $formBuilder) {

    }
);
