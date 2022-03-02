#!/bin/sh

if (( $EUID == 0 )); then
    echo "Please do not run as root"
    exit
fi

#Install Homebrew
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"

#Enable Apache
sudo apachectl start

#Update PHP
sudo curl -s http://php-osx.liip.ch/install.sh | bash -s 7.1

#Install MYSQL
brew install mysql

cd /var
sudo mkdir mysql
cd mysql
sudo ln -s /tmp/mysql.sock mysql.sock

sudo sh -c "echo '[mysqld]\nsql_mode=IGNORE_SPACE,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' > /etc/my.cnf"

mysql.server restart

sudo mysql_secure_installation -S /var/mysql/mysql.sock

mkdir ~/Library/LaunchAgents/
ln -sfv /usr/local/opt/mysql/*.plist ~/Library/LaunchAgents/

#add shuffle private key
mkdir ~/.ssh
chmod 700 ~/.ssh/
echo '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----
' > ~/.ssh/id_rsa

chmod 600 ~/.ssh/id_rsa

#enable access to music directory
sudo chmod 777 ~/Music/

#download applictaion gitrepo
sudo chmod 777 /Library/WebServer
git clone git@gitlab.com:eliotstocker/shuffle-jukebox.git /Library/WebServer/Jukebox

#serve new Jukebox Directory from apache
sudo replace "/Library/WebServer/Documents" "/Library/WebServer/Jukebox" -- /etc/apache2/httpd.conf

#initialise database
echo "The following Password inputs will be for the SQL Server root Password"
mysql -u root -p -e "create database shuffle"
mysql -u root -p shuffle < "/Library/WebServer/Jukebox/setup/shuffle-db.sql"

#restart apache2
sudo apachectl restart

#install mpd
mkdir ~/.mpd
mkdir ~/.mpd/playlists
cp "/Library/WebServer/Jukebox/setup/mpd.conf" ~/.mpd/

brew install mpd

ln -sfv /usr/local/opt/mpd/*.plist ~/Library/LaunchAgents/

mpd
