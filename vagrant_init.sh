# Refresh packages
apt-get update

# Server software
apt-get install -y apache2
apt-get install -y php5
apt-get install -y php5-json
apt-get install -y mysql-server

# Additional utilities
apt-get install -y htop
apt-get install -y curl
apt-get install -y git


# Configure stuff? i.e. apache
rm -rf /var/www
ln -fs /vagrant /var/www

cd /vagrant

ln -s config.php config.php.dist

# Dev libs and tools
curl -sS https://getcomposer.org/installer | php

php /vagrant/composer.phar install