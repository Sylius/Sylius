# Before You Begin

## Pre–Getting Started Guide: What to Know Before Learning Sylius

To get the most out of Sylius, especially version 2.x, we recommend brushing up on the following topics:

**✅ PHP Basics & Modern Practices**

* [PHP: The Right Way](https://phptherightway.com/) – A solid overview of modern PHP best practices.
* [Namespaces and Autoloading (PHP Manual)](https://www.php.net/manual/en/language.namespaces.php)
* [PSR-4 Autoloading (PHP-FIG)](https://www.php-fig.org/psr/psr-4/)
* [OOP in PHP (SymfonyCasts)](https://symfonycasts.com/screencast/oo)

**✅ Symfony Fundamentals (Required for Sylius)**

* [Symfony Routing](https://symfony.com/doc/current/routing.html)
* [Controllers in Symfony](https://symfony.com/doc/current/controller.html)
* [Services and Dependency Injection](https://symfony.com/doc/current/service_container.html)
* [Events and Event Subscribers](https://symfony.com/doc/current/event_dispatcher.html)
* [Doctrine ORM in Symfony](https://symfony.com/doc/current/doctrine.html)
* [Twig Templating](https://twig.symfony.com/doc/3.x/)

**📚 Extra (Nice to Know Before Diving Deeper)**

* Symfony Flex (for project setup and recipes)
* [Symfony Forms](https://symfony.com/doc/current/forms.html) (used heavily in Sylius admin)
* [SymfonyCasts: Symfony 7 Track](https://symfonycasts.com/tracks/symfony)

**👨‍🎓 Sylius Academy**

* [Sylius Practical Mastery Course](https://academy.sylius.com/course/sylius-practical-mastery-course/)

***

## System Requirements

Before you dive into Sylius, your local environment must first meet some requirements to make it possible.

| **\*nix-based Operating System** (macOS, Linux, Windows \[[WSL](https://learn.microsoft.com/en-us/windows/wsl/install) only])                                                                                                                                                             |
| ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| <p><strong>PHP</strong> <code>8.2</code>or higher<br>with the following <a data-footnote-ref href="#user-content-fn-1">extensions</a>:</p><ul><li><code>gd</code></li><li><code>exif</code></li><li><code>fileinfo</code></li><li><code>intl</code></li><li><code>sodium</code></li></ul> |
| [**Composer**](https://getcomposer.org/download/)                                                                                                                                                                                                                                         |
| <p>One of the supported <strong>database</strong> engines:</p><ul><li>MySQL <code>8.0</code> or higher</li><li>MariaDB <code>10.4.10</code> or higher</li><li>PostgreSQL <code>13.9</code> or higher</li></ul>                                                                            |
| [**Node.js**](https://nodejs.org/en) `^20 \|\| ^22`                                                                                                                                                                                                                                       |
| **Git**                                                                                                                                                                                                                                                                                   |

{% hint style="info" %}
If you are planning to develop Sylius directly on your machine (without using e.g. containerization), it is recommended to [install Symfony CLI](https://symfony.com/download) and use [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html).
{% endhint %}

[^1]: These extensions are installed and enabled by default in most PHP 8 installations.
