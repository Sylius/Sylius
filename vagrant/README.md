# Description
This configuration includes following software:

* PHP 5.4.19 
* MySQL 5.5.32
* GIT 1.7.9.5
* Apache 2.2.22
* Vim
* MC (Midnight commander)
* Curl
* Xdebug
* Composer

# Usage

First you need to install git submodules. Go to your project root folder and execute following commands:
```
$ git submodule init
$ git submodule update
```

Now you are ready to run

```
$ cd vagrant
$ vagrant up
```

While waiting for the vagrant to stand up you should add an entry into /etc/hosts file at host machine.

```
10.0.0.200      sylius.dev
```

From now you should be able to access your sylius project at host machine under http://sylius.dev/ address.
