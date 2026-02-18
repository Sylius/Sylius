<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_theming'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.hook.test_theme',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.theme',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.theme',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.theme'
            )
            ->withFilter(new TagFilter('@theming&&@ui'))));
