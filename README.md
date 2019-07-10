# README #

# Ben Lido website
The WooCommerce site for Ben Lido

## USING VVV
This is the latest way for us to set up our environment so that we do not have to worry about how to set up nginx (or apapche), mysql (or mariadb), php (or php-fpm).

### Requirements
Here is the documentation: https://varyingvagrantvagrants.org/docs/en-US/installation/

Need (more info: https://varyingvagrantvagrants.org/docs/en-US/installation/software-requirements/):

1. VirtualBox 5.x+ (6.+ is also okay) [ https://www.virtualbox.org/wiki/Downloads ] 
2. Vagrant 2.1+ [ https://www.vagrantup.com/downloads.html ]

## Installation

NOTE!!!!! YOU WILL NEED TO Re-Check Out this code after setting up VVV.

Basically, you set up VVV and everything first, then, you will need to check out this repo inside the VVV www directory.
Then, you need to create a "provision" profile to provision and run this code from inside the www directory.
When you change code inside the newly checked out code.

For example, line #2 below will create a directory in your home directory called vagrant-local.
Inside the vagrant-local, you will see a directory called www.
cd into that www directory, then do the git clone there.

If this is not the first time you are running vvv, then you should already have a vvv-custom.yml file.
Skip steps #1 to #5. Start with Step #6.

1. Install VirtualBox and Vagrant
2. Install VVV: git clone -b master git://github.com/Varying-Vagrant-Vagrants/VVV.git ~/vagrant-local
3. cd ~/vagrant-local
4. install required vagrant plugin: ```vagrant plugin install vagrant-hostsupdate```
5. copy vvv-config.yml to vvv-custom.yml ( ```cp vvv-config.yml vvv-custom.yml``` ) in the same directory (NOTE: only do this once. If you have already created a vvv-custom.yml, then just copy the site config lines to here)
6. copy the site config lines from below into the vvv-custom.yml
7. run ```vagrant up```
8. ```cd www```
9. ```git clone https://{YOUR_USERNAME}@bitbucket.org/urbanpixels/ben-lido.git```
10. ```cd ben-lido```
11. ```git checkout basel-base``` 
12. ```cd ~/vagrant-local```
13. ```vagrant provision --provision-with site-ben-lido```  you will need to enter your password to allow the script to update the /etc/hosts file for you
14. view site in your browser at http://local.benlido.test/

### VVV configuration
Below is the configuration that needs to go into the vvv-custom.yml file.. make sure it goes below the "sites:" line. Also, DO NOT USE TABS.. because it's YAML, it needs to be spaces. Save it before you continue with step #6.

If this is not the first time you are setting up vvv, then just add the lines to your existing vvv-custom.yml and proceed with step 6

```

  ben-lido:
    hosts:
     - local.benlido.test
    custom:
      db_name: ben_lido
      orig_host: dev.benlido.com
      wp_admin: admin
      wp_pass: aftertenthwave
      http_user: benlido
      http_passwd: benlido2018
      get_database: 1
      get_files: 1
      
```


## Site setup requirements

* DocumentRoot is wordpress/
* Easiest is to use this hostname: benlido.urbanpixels.localhost
* in config/db/ben_lido.sql (uncompress first), do a global search and replace for benlido.urbanpixels.localhost => to whatever your hostname is. If you are not sure what the hostname already in the DB is, just open the file and search for "siteurl". NOTE: you can also use wp-cli. See below.
* create wp-content/uploads folder and give it write permission
* create wp-content/cache folder and gie it write permission
* the wp-config.php currently has the localhost config using the root user and no password. If you want to change this, please add another environment (maybe something like LOCAL_{YOUR_NAME})

## how this site was set up:
NOTE: YOU DO NOT NEED TO DO THIS. THIS IS HERE FOR REFERENCE
Using wp-cli: http://wp-cli.org/

```
wp core download
wp core config --dbname=ben_lido --dbuser=root 
wp core install --url="benlido.urbanpixels.localhost" --admin_user="admin" --admin_password="aftertenthwave" --admin_email="dave@urbanpixels.com" --title="Ben Lido"
```

### Some useful wp-cli commands 

```
wp db import wordpress_sql_file.sql
wp search-replace benlido.urbanpixels.localhost your-hostname.urbanpixels.localhost
wp option set woocommerce_force_ssl_checkout 'no'
wp check-update
```

It also uses composer: https://getcomposer.org/
to manage the plugins.

once you have composer set up, just go to

```
cd wordpress
composer update --prefer-dist
```

It uses the wordpress/composer.json and the wordpress/composer.lock file to manage the free plugins.
Just add the plugin name (make sure it's available in: http://wpackagist.org before adding) into the composer.json file.

For custom-built plugins, just add directly into the wp-content/plugins/ directory.

NOTE: we cannot manage paid plugins this way because it usually requires login walls. We will need to download the plugin and manually update.
**IMPORTANT** make sure you use the --prefer-dist flag. Otherwise, any plugins that are in a git repo will have their .git directory copied over, and will collide with this repo.

### for Apache Virtualhost:

do somethig like this:

```
<Directory /Users/dave/Sites/ben-lido/wordpress>
        Require all granted
        Options Includes FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
</Directory>
```

## Data Related
Below are instructions for data related stuff

### Delete all products
```
-- Remove all attributes from WooCommerce
DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%');
DELETE FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%';
DELETE FROM wp_term_relationships WHERE term_taxonomy_id not IN (SELECT term_taxonomy_id FROM wp_term_taxonomy);
-- Delete all WooCommerce products
DELETE FROM wp_term_relationships WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('product','product_variation'));
DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('product','product_variation'));
DELETE FROM wp_posts WHERE post_type IN ('product','product_variation');
-- Delete orphaned postmeta
DELETE pm
FROM wp_postmeta pm
LEFT JOIN wp_posts wp ON wp.ID = pm.post_id
WHERE wp.ID IS NULL;
```


## Basel Theme info

Current theme: Ben Lido Basel Child
Theme Settings: 
- Header: 
    - Upload logo image
    - Header Layout (Simplified)
    - Main menu align (Center)
    - Other -> Display wishlist icon (Off)
- Footer: 
    - Footer layout (Four Column)
    - Copyrights (Off)
    

AJAX Add to cart: /themes/basel/js/functions.js



