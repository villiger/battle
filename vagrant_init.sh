# Server software
apt-get install -y apache2
apt-get install -y php5.6
apt-get install -y mysql

# Dev libs and tools
curl -sS https://getcomposer.org/installer | php

# Additional utilities
apt-get install -y htop


# Configure stuff? i.e. apache
rm -rf /var/www
ln -fs /vagrant /var/www

php /vagrant/composer.json install