<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\Extend;
use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\IsFinal;
use Arkitect\Expression\ForClasses\NotDependsOnTheseNamespaces;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\Rule;
use PhpSpec\ObjectBehavior;

return static function (Config $config): void
{
    $srcClassSet = ClassSet::fromDir(__DIR__.'/src');

    $rules = [];

    $rules[] = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Sylius\Component'))
        ->should(new NotDependsOnTheseNamespaces('Sylius\Bundle'))
        ->because('Sylius components should be stand-alone')
    ;

    $rules[] = Rule::allClasses()
        ->that(new Extend(ObjectBehavior::class))
        ->should(new HaveNameMatching('*Spec'))
        ->because('This is a convention from PHPSpec')
    ;

    $rules[] = Rule::allClasses()
        ->that(new Extend(ObjectBehavior::class))
        ->should(new IsFinal())
        ->because('Specifications should not be extendable')
    ;

    $config->add($srcClassSet, ...$rules);
};
