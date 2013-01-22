# Getting started using Vagrant

## You will need:

  * Git 1.6+
  * NFS (MacOS works OOB, on Debian based linux distributions install **nfs-kernel-server** package)
  * [Vagrant](http://vagrantup.com)

## Get the code

    git clone git://github.com/Sylius/Sylius.git
    cd Sylius/vagrant
    vagrant up

Now everything is getting prepared.  
In the meantime you can optionally add an entry to your `/etc/hosts` file like so:

    172.33.33.33 sylius.local

## Access by web browser

If you have added the entry to `/etc/hosts` you should be able to access the Sylius like this:

<http://sylius.local/dev.php>

Otherwise you can also use the IP address:

<http://172.33.33.33/dev.php>
