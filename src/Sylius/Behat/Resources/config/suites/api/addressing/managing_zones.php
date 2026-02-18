<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_managing_zones', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.province',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.zone',
                'sylius.behat.context.transform.zone_member',
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.api.admin.managing_zones',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.admin.save',
                'sylius.behat.context.api.debug'
            )
            ->withFilter(new TagFilter('@managing_zones&&@api'))));
