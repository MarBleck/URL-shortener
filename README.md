# URL-shortener
This is an URL shortener written in php with connection to mysql

## How to use
You have to clone this reposetory 

# URL-shortener

## About
This URL shortener is written in PHP, and it's lightweight with all basic features and some extra features.
### Requirements
- Web server `Apache` or `Nginx`
- PHP no matter which version
- MySQL or MariaDB
### Included services:
- 'URL shortener' => Short all your url's
- 'Login' => The login mask to log into your URL shortener
- 'Signup' => Also with it is one signup page with signup token support / this service can easily disable
- 'Change username' => You can change your username you want
- 'Change password' => You can change your password also in this system
- 'Change permissions' => You can change your permission level from `user` to `admin` or from `admin` to `user` with a token / this service can easily disable
- 'Delete account' => When you or users want to delete their account (GDPR) / this service can easily disable
- 'Create user' => When signup is disabled, and you want to create a new user (admin rights provided)
- 'Delete user' => When you want to delete another user from this platform (admin rights provided)
- 'Update permissions from another user' => When you want to change permissions from another user (admin rights provided)
## Usage
Just ssh into your server and clone this git repository. Make sure you have installed `git`.
```bash
apt update
apt -y install git
```
### Clone this Repository
```bash
git clone https://github.dev/marlonbde/URL-shortener.git
```
### Configuration
#### MySQL database
- Create a database with the name you want.
- Create a user with a secure password.
- Grant all permissions on the database to this user.
```bash
mysql
CREATE DATABASE your_database_name;
CREATE USER 'username'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON your_database_name . * TO 'username'@'localhost';
FLUSH PRIVILEGES;
exit;
```
#### Configuration config.inc.php
- Copy the content out of the folder into your web root folder directory.
- Edit in the folder the `inc/config.inc.php` there you find all the configurations settings.
- Make sure you have configured right the database connection.
- When you have changed the config you want to have it, then you are ready.
- Then you must edit the init.php in the web root directory there you have to type in your username and your password for the new user, this file will delete after initialization.
##### Execute this command for initialization
```bash
curl -q https://your_server_name/init.php
rm init.php
```
##### When you use nginx as Web server
- Please make sure you have the location tags out of the `nginx-location.conf` pasted into your nginx virtual host config and deleted all old location tags.
### Use
- Please make sure that you deleted all extra stuff how `nginx-location.conf` and `README.md` this can handycap the URL shortener
- After initialization go to your URL shortener instance URL, you will automatically be redirected to the login page.
- Log in and have fun with this program.