# Set non interactive setup (mainly for mysql-server)
export DEBIAN_FRONTEND=noninteractive

# Refresh packages
apt-get update

# Server software
apt-get install -q -y apache2
apt-get install -q -y php5
apt-get install -q -y php5-json
apt-get install -q -y php5-mysql
apt-get install -q -y php5-curl
apt-get install -q -y mysql-server

# Additional utilities
apt-get install -q -y htop
apt-get install -q -y curl
apt-get install -q -y git


# Configure stuff? i.e. apache
# Apache
rm -rf /var/www
ln -fs /vagrant /var/www

# Enable mod_rewrite http://www.dev-metal.com/enable-mod_rewrite-ubuntu-12-04-lts/
sudo a2enmod rewrite
sudo service apache2 restart
sudo cp /etc/apache2/apache2.conf /etc/apache2/apache2.conf.backup
sudo cat /etc/apache2/apache2.conf | sed 's/AllowOverride None/AllowOverride All/g' > /etc/apache2/apache2.conf.new
sudo cp /etc/apache2/apache2.conf.new /etc/apache2/apache2.conf
sudo service apache2 restart

mysqladmin create -uroot battle

cd /vagrant

#When running on windows, creating links seems to throw "Protocol Error". Copying instead
#ln -s config.php.dist config.php
cp config.php.dist config.php

# Dev libs and tools
curl -sS https://getcomposer.org/installer | php

php /vagrant/composer.phar install