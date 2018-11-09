FROM ubuntu:12.04
LABEL Description="Basic LAMP stack for legacy PHP web application boilerplate" \
            Usage="docker run --detach --publish 8080:80 --volume $PWD:/var/www/legacy" 

# Wrap to single run to avoid multilayer build cache.
# It would be easier to read the apache config and docker scripts
# if they would be copied from separate file into container,
# but we don't want to pollute the project directory with
# files related only to building development environment.
# Entrypoint script tries to read host uid and gid of 
# apache document root volume and runs the web server 
# with that user and group. Both are added to container if not exists.
RUN apt-get update;\
    apt-get install -y --only-upgrade ca-certificates libssl1.0.0 libcurl3-gnutls libgcrypt11 libss2;\
    echo mysql-server mysql-server/root_password password secret | debconf-set-selections;\
    echo mysql-server mysql-server/root_password_again password secret | debconf-set-selections;\
    apt-get install -y \
        nano \ 
        curl \
        zip \
        unzip \
        apache2 \
        libapache2-mod-php5 \
        php5-curl \
        php5-mcrypt \
        mysql-server \
        php5-mysql;\
    mkdir /var/www/legacy;\
    printf '\n\
        <VirtualHost *:80>\n\
            ServerAdmin webmaster@localhost\n\ 
            DocumentRoot /var/www/legacy/public\n\ 
            <Directory />\n\
                Options FollowSymLinks\n\
                AllowOverride None\n\
            </Directory>\n\
            <Directory /var/www/legacy/public/>\n\
                Options Indexes FollowSymLinks MultiViews\n\
                AllowOverride All\n\
                Order allow,deny\n\
                allow from all\n\
            </Directory>\n\
        </VirtualHost>\n'\
    >> /etc/apache2/sites-available/legacy;\
    a2dissite default;\
    a2ensite legacy;\
    a2enmod rewrite;\
    printf '#!/usr/bin/env bash\n\
\n\
export COMPOSER_HOME=/var/www/legacy\n\
\n\
if [ "$1" = "start-web-services" ]; then\n\
    userid=$( stat -c"%%u" /var/www/legacy )\n\
    getent passwd "$userid" > /dev/null\n\
    if [ "$?" -ne 0 ]; then\n\
        useradd -u "$userid" "docku-$userid"\n\
        apache_user="docku-$userid"\n\
    else\n\
        apache_user=$( getent passwd "$userid" | cut -f1 -d":" )\n\
    fi\n\
    if [ "$apacher_user" = "root" ]; then apache_user="www-data"; fi\n\
\n\
    groupid=$( stat -c"%%g" /var/www/legacy )\n\
    getent group "$groupid" > /dev/null\n\
    if [ "$?" -ne 0 ]; then\n\
        groupadd -g "$groupid" "dockg-$groupid"\n\
        apache_group="dockg-$groupid"\n\
    else\n\
        apache_group=$( getent group "$groupid" | cut -f1 -d":" )\n\
    fi\n\
    if [ "$apache_group" = "root" ]; then apache_group="www-data"; fi\n\
\n\
    /usr/bin/mysqld_safe &\n\
    trap "killall mysqld" SIGTERM\n\
\n\
    sed -i -e "s/APACHE_RUN_USER=.*$/APACHE_RUN_USER=$apache_user/" -e "s/APACHE_RUN_GROUP=.*$/APACHE_RUN_GROUP=$apache_group/" /etc/apache2/envvars\n\
    apachectl -DFOREGROUND -k start\n\
else\n\
    set -e\n\
    exec "$@"\n\
fi\n'\
>> /usr/bin/docker-entrypoint.sh;\
    printf '#!/usr/bin/env bash\n\
apache_user=$( egrep -o "APACHE_RUN_USER=(.*)$" /etc/apache2/envvars  | sed "s/APACHE_RUN_USER=//" )\n\
su - "$apache_user" -s/bin/bash  <<"EOC"\n\
    export COMPOSER_HOME=/var/www/legacy\n\
    cd $COMPOSER_HOME\n\
    curl -sS https://getcomposer.org/installer | php\n\
    php composer.phar install --no-progress --no-suggest --no-interaction --no-ansi\n\
    bash phing install\n\
    sed -i -e "s/DB_USER=.*$/DB_USER=root/" -e "s/DB_PASS=.*$/DB_PASS=secret/" ./config/config.file\n\
    source ./config/config.file\n\
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE $DB_NAME"\n\
    bash phing migration-run\n\
    echo "Done..."\n\
EOC\n'\
>> /usr/bin/docker-install-legacy-project.sh;\
    chmod +x /usr/bin/docker-*.sh

VOLUME /var/www
VOLUME /var/lib/mysql

EXPOSE 80

ENTRYPOINT ["/usr/bin/docker-entrypoint.sh"]
CMD ["start-web-services"]
