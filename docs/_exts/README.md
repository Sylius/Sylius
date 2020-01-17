Sphinx Extensions for PHP and Symfony
=====================================

You can install the extension by:

 * running `sudo pip install git+https://github.com/fabpot/sphinx-php.git`;

 * cloning the project and add `sensio` to your path (with something like
   `sys.path.insert(0, os.path.abspath('./path/to/sensio'))`).
   
 * Arch Linux users can use the [AUR package](https://aur.archlinux.org/packages/python-sphinx-php-git/)

You can use the following extensions in your `conf.py` file:

 * `sensio.sphinx.refinclude`
 * `sensio.sphinx.configurationblock`
 * `sensio.sphinx.phpcode`
 * `sensio.sphinx.bestpractice`

To enable highlighting for PHP code not between `<?php ... ?>` by default:

    # loading PhpLexer
    from sphinx.highlighting import lexers
    from pygments.lexers.web import PhpLexer
    
    # enable highlighting for PHP code not between ``<?php ... ?>`` by default
    lexers['php'] = PhpLexer(startinline=True)
    lexers['php-annotations'] = PhpLexer(startinline=True)

And here is how to use PHP as the primary domain:

    primary_domain = 'php'

Configure the `api_url` for links to the API:

    api_url = 'http://api.symfony.com/master/%s'
