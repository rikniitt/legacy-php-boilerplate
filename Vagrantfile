# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "ubuntu/trusty64"

  config.vm.hostname = "legacy-box"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network "forwarded_port", guest: 80, host:8080

  config.vm.synced_folder ".", "/vagrant", owner: "www-data", group: "vagrant"

  config.vm.provider "virtualbox" do |v|
    v.memory = 2048
    v.cpus = 2
  end

  box_config = {
    :mysql_root_password => "root"
  }

  # Setup apache, mysql and php
  config.vm.provision "shell", inline: <<-SHELL
    apt-get update
    debconf-set-selections <<< "mysql-server mysql-server/root_password password #{box_config[:mysql_root_password]}"
    debconf-set-selections <<< "mysql-server mysql-server/root_password_again password #{box_config[:mysql_root_password]}"
    apt-get install -y curl apache2 libapache2-mod-php5 php5-curl php5-mcrypt mysql-server libapache2-mod-auth-mysql php5-mysql
    echo ServerName $HOSTNAME >> /etc/apache2/apache2.conf
    cat > /etc/apache2/sites-available/001-legacy.conf <<EOL
<VirtualHost *:80>
  ServerAdmin webmaster@localhost
  DocumentRoot /vagrant/public
  <Directory />
    Options FollowSymLinks
    AllowOverride None
  </Directory>
  <Directory /vagrant/public/>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
EOL
    a2dissite 000-default.conf
    a2ensite 001-legacy.conf
    a2enmod rewrite
    service apache2 restart
  SHELL

  # Install project
  config.vm.provision "shell", privileged:false, inline: <<-SHELL
    cd /vagrant
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install --no-progress --no-suggest --no-interaction --no-ansi
    ./robo install
    # Setup db
    sed -i "s/DB_USER='username'/DB_USER='root'/" ./config/config.file
    sed -i "s/DB_PASS='password'/DB_PASS='#{box_config[:mysql_root_password]}'/" ./config/config.file
    source ./config/config.file
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE $DB_NAME"
    ./robo migration:run
  SHELL

end
