$host_name = "sylius.dev"
$db_name = "sylius"

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

# when bumping the php version again, please
# have a look at the PPA documentary in order to provide a proper PPA alias:
# https://launchpad.net/~ondrej/+archive/ubuntu/php5
apt::ppa { 'ppa:ondrej/php5':
  require => Apt::Key['4F4EA0AAE5267A6C']
}

package { [
    'build-essential',
    'vim',
    'curl',
    'git-core',
    'mc',
    'openjdk-7-jre-headless'
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
    'VAGRANT VAGRANT'
  ],
  priority      => '1',
}

class { 'php':
  service             => 'apache',
  service_autorestart => false,
}

php::module { 'mysql': }
php::module { 'cli': }
php::module { 'curl': }
php::module { 'intl': }
php::module { 'mcrypt': }
php::module { 'gd': }
php::module { 'apc':
  module_prefix => 'php-',
}

class { 'php::devel':
  require => Class['php'],
}

class { 'php::pear':
  require => Class['php'],
}

php::pecl::module { 'mongo':
    use_package => "no",
}

class { 'composer':
  command_name => 'composer',
  target_dir   => '/usr/local/bin',
  auto_update => true,
  require => Package['php5', 'curl'],
}

php::ini { 'php_ini_configuration':
  value   => [
    'extension=mongo.so',
    'date.timezone = "UTC"',
    'display_errors = On',
    'error_reporting = -1',
    'short_open_tag = 0',
  ],
  notify  => Service['apache'],
  require => Class['php']
}

class { 'mysql::server':
  override_options => { 'root_password' => '', },
}

database{ "${db_name}":
  ensure  => present,
  charset => 'utf8',
  require => Class['mysql::server'],
}

database{ "${db_name}_dev":
  ensure  => present,
  charset => 'utf8',
  require => Class['mysql::server'],
}

database{ "${db_name}_test":
  ensure  => present,
  charset => 'utf8',
  require => Class['mysql::server'],
}

class { 'elasticsearch':
  ensure => 'present',
  package_url => 'https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.3.4.deb'
} ->
service { "elasticsearch-service":
  name => 'elasticsearch',
  ensure => running
}
