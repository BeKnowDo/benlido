#!/usr/bin/env bash
# Provision WordPress Stable

# fetch the first host as the primary domain. If none is available, generate a default using the site name
DOMAIN=`get_primary_host "${VVV_SITE_NAME}".test`
SITE_TITLE=`get_config_value 'site_title' "${DOMAIN}"`
WP_VERSION=`get_config_value 'wp_version' 'latest'`
WP_TYPE=`get_config_value 'wp_type' "single"`
DB_NAME=`get_config_value 'db_name'`
DB_NAME=${DB_NAME//[\\\/\.\<\>\:\"\'\|\?\!\*-]/}
ORIG_HOST=`get_config_value 'orig_host'`
WP_ADMIN=`get_config_value 'wp_admin' "admin"`
WP_PASS=`get_config_value 'wp_pass' "password"`
HTTP_USER=`get_config_value 'http_user'`
HTTP_PASSWD=`get_config_value 'http_passwd'`
GET_DATABASE=`get_config_value 'get_database'`
GET_FILES=`get_config_value 'get_files'`

# Nginx Logs
echo -e "Creating log directories..."
mkdir -p ${VVV_PATH_TO_SITE}/log
touch ${VVV_PATH_TO_SITE}/log/nginx-error.log
touch ${VVV_PATH_TO_SITE}/log/nginx-access.log

# Make a database, if we don't already have one
echo -e "Creating database '${DB_NAME}' (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME}"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO wp@localhost IDENTIFIED BY 'wp';"
if [ -f "${VVV_PATH_TO_SITE}/wordpress/wp-config-local.php"]; then
  echo "local wp-config file exists"
else 
  cp ${VVV_PATH_TO_SITE}/provision/wp-config-local.php ${VVV_PATH_TO_SITE}/wordpress/
fi

cd ${VVV_PATH_TO_SITE}/wordpress


if [[ "$GET_FILES" == "1" ]]; then
  echo "Downloading files..."
  noroot wget --http-user=$HTTP_USER --http-password=$HTTP_PASSWD https://$ORIG_HOST/wp-content/uploads.tgz --directory-prefix=${VVV_PATH_TO_SITE}/wordpress/wp-content/
  
  echo "Extracting uploads.tgz..."
  cd ${VVV_PATH_TO_SITE}/wordpress/wp-content
  noroot tar -zvxf ${VVV_PATH_TO_SITE}/wordpress/wp-content/uploads.tgz
  noroot mv ${VVV_PATH_TO_SITE}/wordpress/wp-content/www/dev.benlido.com/wordpress/wp-content/uploads ${VVV_PATH_TO_SITE}/wordpress/wp-content/
  noroot chmod -R 777 ${VVV_PATH_TO_SITE}/wordpress/wp-content/uploads
  noroot rm -Rf ${VVV_PATH_TO_SITE}/wordpress/wp-content/www

  echo "Cleaning up..."
  noroot rm -Rf ${VVV_PATH_TO_SITE}/wordpress/wp-content/uploads.*
fi


if [ "$GET_DATABASE" == "1" ] && [ "$GET_FILES" == "1" ]; then
  echo "Extracting database..."
  noroot gunzip -c "${VVV_PATH_TO_SITE}/wordpress/wp-content/uploads/dev.benlido.com.sql.gz" > ${VVV_PATH_TO_SITE}/wordpress/db.sql

  echo "Importing DB copy..."
  noroot wp db import ${VVV_PATH_TO_SITE}/wordpress/db.sql
  echo "Modifying site URL"
  noroot wp search-replace dev.benlido.com $DOMAIN
  noroot wp search-replace https://$DOMAIN http://$DOMAIN
  noroot wp option set woocommerce_force_ssl_checkout 'no'
  echo "Cleaning up DB import file"
  noroot rm ${VVV_PATH_TO_SITE}/wordpress/db.sql
fi



#echo "Updating site URLs for local development..."
#noroot wp search-replace ${ORIG_HOST} ${DOMAIN}

