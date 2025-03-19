# System Requirements

Before you dive into Sylius, your local environment must first meet some requirements to make it possible.

| \*nix-based Operating System (macOS, Linux, Windows \[[WSL](https://learn.microsoft.com/en-us/windows/wsl/install) only])                                                                                                                    |
| -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| <p>PHP <code>8.2</code>or higher<br>with the following <a data-footnote-ref href="#user-content-fn-1">extensions</a>:</p><ul><li><code>gd</code></li><li><code>exif</code></li><li><code>fileinfo</code></li><li><code>intl</code></li></ul> |
| [Composer](https://getcomposer.org/download/)                                                                                                                                                                                                |
| <p>One of the supported database engines:</p><ul><li>MySQL <code>8.0</code> or higher</li><li>MariaDB <code>10.4.10</code> or higher</li><li>PostgreSQL <code>13.9</code> or higher</li></ul>                                                |
| [Node.js](https://nodejs.org/en) `^20 \|\| ^22`                                                                                                                                                                                              |
| Git                                                                                                                                                                                                                                          |

{% hint style="info" %}
If you are planning to develop Sylius directly on your machine (without using e.g. containerization), it is recommended to [install Symfony CLI](https://symfony.com/download) and use [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony\_server.html).
{% endhint %}

[^1]: These extensions are installed and enabled by default in most PHP 8 installations.
