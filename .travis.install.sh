set -x
if [ "$TRAVIS_PHP_VERSION" = 'hhvm' ]; then
sudo apt-get remove hhvm
    sudo add-apt-repository -y ppa:mapnik/boost
    sudo apt-get -y --force-yes update
    sudo apt-get -y --force-yes install hhvm-nightly
    hhvm --version

    curl -sS https://getcomposer.org/installer | hhvm
    hhvm -v ResourceLimit.SocketDefaultTimeout=30 -v Http.SlowQueryThreshold=30000 composer.phar require phpunit/phpunit=$PHPUNIT_VERSION
    hhvm -v ResourceLimit.SocketDefaultTimeout=30 -v Http.SlowQueryThreshold=30000 composer.phar install
else
    composer require phpunit/phpunit=$PHPUNIT_VERSION
    composer install
fi