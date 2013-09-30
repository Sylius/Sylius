$host_name = "sylius.dev"
$db_name = "sylius"
$db_name_dev = "${db_name}-dev"
$db_name_tst = "${db_name}-tst"

group { 'puppet': ensure => present }
Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ] }
File { owner => 0, group => 0, mode => 0644 }

file { "/dev/shm/sylius":
  ensure => directory,
  purge => true,
  force => true,
  owner => vagrant,
  group => vagrant
}

file { "/var/lock/apache2":
  ensure => directory,
  owner => vagrant
}

exec { "ApacheUserChange" :
  command => "sed -i 's/export APACHE_RUN_USER=.*/export APACHE_RUN_USER=vagrant/ ; s/export APACHE_RUN_GROUP=.*/export APACHE_RUN_GROUP=vagrant/' /etc/apache2/envvars",
  require => [ Package["apache"], File["/var/lock/apache2"] ],
  notify  => Service['apache'],
}

class {'apt':
  always_apt_update => true,
}

Class['::apt::update'] -> Package <|
    title != 'python-software-properties'
and title != 'software-properties-common'
|>

    apt::key { '4F4EA0AAE5267A6C': }

apt::ppa { 'ppa:ondrej/php5-oldstable':
  require => Apt::Key['4F4EA0AAE5267A6C']
}

package { [
    'build-essential',
    'vim',
    'curl',
    'git-core',
    'mc'
  ]:
  ensure  => 'installed',
}

class { 'apache': }

apache::dotconf { 'custom':
  content => 'EnableSendfile Off',
}

apache::module { 'rewrite': }

apache::vhost { "${host_name}":
  server_name   => "${host_name}",
  serveraliases => [
    "www.${host_name}"
  ],
  docroot       => "/var/www/sylius/web/",
  port          => '80',
  env_variables => [
],
  priority      => '1',
}

class { 'php':
  service             => 'apache',
  service_autorestart => false,
  module_prefix       => '',
}

php::module { 'php5-mysql': }
php::module { 'php5-cli': }
php::module { 'php5-curl': }
php::module { 'php5-intl': }
php::module { 'php5-mcrypt': }
php::module { 'php5-gd': }
php::module { 'php-apc': }

class { 'php::devel':
  require => Class['php'],
}

class { 'php::pear':
  require => Class['php'],
}

php::pear::module { 'PHPUnit':
  repository  => 'pear.phpunit.de',
  use_package => 'no',
  require => Class['php::pear']
}

php::pecl::module { 'mongo':
    use_package => "no",
}

class { 'xdebug':
  service => 'apache',
}

class { 'composer':
  require => Package['php5', 'curl'],
}

puphpet::ini { 'xdebug':
  value   => [
    'xdebug.default_enable = 1',
    'xdebug.remote_autostart = 0',
    'xdebug.remote_connect_back = 1',
    'xdebug.remote_enable = 1',
    'xdebug.remote_handler = "dbgp"',
    'xdebug.remote_port = 9000'
  ],
  ini     => '/etc/php5/conf.d/sylius_xdebug.ini',
  notify  => Service['apache'],
  require => Class['php'],
}

puphpet::ini { 'mongo':
  value   => [
    'extension=mongo.so',
  ],
  ini     => '/etc/php5/conf.d/sylius_mongo.ini',
  notify  => Service['apache'],
  require => Class['php'],
}

puphpet::ini { 'custom':
  value   => [
    'date.timezone = "UTC"',
    'display_errors = On',
    'error_reporting = -1',
    'short_open_tag = 0',
    'xdebug.max_nesting_level = 1000'
  ],
  ini     => '/etc/php5/conf.d/sylius_custom.ini',
  notify  => Service['apache'],
  require => Class['php'],
}

class { 'mysql::server':
  override_options => { 'root_password' => '', },
}

database{ "sylius":
  ensure  => present,
  charset => 'utf8',
  require => Class['mysql::server'],
}

database{ "sylius_dev":
  ensure  => present,
  charset => 'utf8',
  require => Class['mysql::server'],
}

database{ "sylius_test":
  ensure  => present,
  charset => 'utf8',
  require => Class['mysql::server'],
}