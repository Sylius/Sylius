<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('change_admin_password'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.cli.change_admin_password'
            )
            ->withFilter(new TagFilter('@change_admin_password&&@cli'))));
