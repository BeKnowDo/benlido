# README #

# Ben Lido website
The WooCommerce site for Ben Lido


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
WHERE wp.ID IS NULL
```



