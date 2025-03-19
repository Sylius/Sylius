# Architectural Drivers

Architectural Drivers are the key factors that influence all the decisions we make during application development. Historically, a lot of them were taken unconsciously, but, happily, resulted in good decisions that we can undoubtedly justify today. All of them have a significant influence on the Sylius as an application - they can and should be used to guide us during the development, to make the best decision for the product.

### Technical constraints

#### Programming language

**PHP**

Due to the decision to base Sylius on the **Symfony** framework (see below), **PHP** was the only possible option as a programming language. Nevertheless, a good decision! This language has been dynamically developing for the last few years and still powers up most of the websites and applications on the World Wide Web.

Currently supported PHP versions can be seen in [this chapter.](../installation/system-requirements.md)

#### Main frameworks and libraries

**Fullstack Symfony**

Sylius is based on Symfony, a leading PHP framework for creating web applications. Using Symfony allows developers to work better and faster by providing them with the certainty of developing an application that is fully compatible with the business rules, structured, maintainable, and upgradable. It also allows developers to save time by providing generic reusable modules.

[Learn more about Symfony](https://symfony.com/what-is-symfony).

**Doctrine**

Sylius, by default, uses the Doctrine ORM to manage all entities. Doctrine is a family of PHP libraries focused on providing a data persistence layer. The most important are the object-relational mapper (ORM) and the database abstraction layer (DBAL). One of Doctrine’s key features is the possibility to write database queries in Doctrine Query Language (DQL) - an object-oriented dialect of SQL.

For a deeper understanding of how Doctrine works, please refer to the [excellent documentation on their official website](https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/index.html).

**Twig**

Twig is a modern template engine for PHP that is fast, secure, and flexible. Twig is being used by Symfony.

To read more about Twig, [go here](https://twig.symfony.com/).

**API Platform**

API Platform is a modern solution for developing high-quality APIs. API Platform works by default with Symfony and depends on its components.

**Third-Party Libraries**

Sylius uses a lot of libraries for various tasks:

* [Payum](https://github.com/Payum/Payum) for payments
* [KnpMenu](https://symfony.com/doc/current/bundles/KnpMenuBundle/index.html) - for shop and admin menus
* [Flysystem](https://github.com/thephpleague/flysystem) - for filesystem abstraction (store images locally, Amazon S3 or external server)
* [Imagine](https://github.com/liip/LiipImagineBundle) - for image processing, generating thumbnails, and cropping
* [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) - for pagination

### Functional requirements

All of the functionality provided by default with Sylius is described as user stories using Behat scenarios. Take a look [here](https://github.com/Sylius/Sylius/tree/2.0/features) to browse them.

### Quality attributes

Sylius has focused a lot on software quality since its very beginning. We use test-driven methodologies like [TDD and BDD](broken-reference) to ensure the reliability of the provided functionalities. Moreover, as Sylius is not the end-project (it is rarely used in a _vanilla_ version), but serves as the base for the actual applications, it’s crucial to take care about its ability to fulfill such a role.

#### Extendability

Sylius offers a lot of standard e-commerce features, that could and should be used as a base to introduce more advanced and business-specific functionalities.

**Question to be asked:** is it possible to easily add new, more advanced functionality to the module/class/service I implement? **Examples:**

* promotions [actions](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Bundle/CoreBundle/Resources/config/services/promotion.xml) and [rules](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Bundle/PromotionBundle/Resources/config/services.xml) registered with tags
* state machine [callbacks](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Bundle/CoreBundle/Resources/config/app/state\_machine/sylius\_order.yml)
* resource [events](https://github.com/Sylius/SyliusResourceBundle/blob/1.12/src/Bundle/Controller/ResourceController.php#L175)

#### Customizability

Seemingly similar to the previous one, but essentially different. Focuses on making it possible to override the standard functionality with a different one, while still keeping the whole process working. The most important (but not the only) step to reach it is using interfaces with small, focused, and granular services. Customizability should be kept on all levels - from the single service to the whole module/component.

**Question to be asked:** is it possible to replace this functionality and not break the whole process? **Examples:**

* service for [calculating the variant prices](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Component/Core/Calculator/ProductVariantPriceCalculator.php) that can be overridden to provide more advanced pricing strategies
* [resource configuration](https://github.com/Sylius/SyliusResourceBundle/blob/1.12/docs/reference.md), which gives possibility to configure any service as a resource-specific controller/factory/repository etc.

#### Testability

As mentioned before, Sylius embraces test-driven methodologies from its very beginning. Therefore, every class (with some exceptions) should be described with unit tests, every functionality should be designed through Behat acceptance scenarios. Highly tested code is crucial to ensure another, also important driver, which is the **reliability** of the software.

**Question to be asked:** is my module/class easy to be tested, to protect it from potential regression?

As history has shown, if something is difficult to test, there is a huge chance it’s not designed or written properly.

### Sources and inspirations

This chapter was created and inspired by the following sources:

* [Architectural Drivers in Modern Software Architecture](https://medium.com/@janerikfra/architectural-drivers-in-modern-software-architecture-cb7a42527bf2) by Erik Franzen
* [Modular Monolith: Architectural Drivers](http://www.kamilgrzybek.com/design/modular-monolith-architectural-drivers/) by Kamil Grzybek
* 🇵🇱 [Droga Nowoczesnego Architekta](https://droganowoczesnegoarchitekta.pl/) - a polish online course for software architects and engineers\
