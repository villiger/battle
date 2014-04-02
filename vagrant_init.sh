# Set non interactive setup (mainly for mysql-server)
export DEBIAN_FRONTEND=noninteractive

# Refresh packages
apt-get update

# Server software
apt-get install -q -y apache2
apt-get install -q -y php5
apt-get install -q -y php5-json
apt-get install -q -y mysql-server

# Additional utilities
apt-get install -q -y htop
apt-get install -q -y curl
apt-get install -q -y git


# Configure stuff? i.e. apache
rm -rf /var/www
ln -fs /vagrant /var/www
mysqladmin create -uroot battle

cd /vagrant

#When running on windows, creating links seems to throw "Protocol Error". Copying instead
#ln -s config.php.dist config.php
cp config.php.dist config.php

# Dev libs and tools
curl -sS https://getcomposer.org/installer | php

php /vagrant/composer.phar install