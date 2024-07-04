#!/bin/sh

# remember to make this file executable: chmod +x set-folder-permissions-in-nginx-container.sh

#chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/

# Nginx has a /docker-entrypoint.d folder which contains scripts that are executed when its started,
#   In the Dockerfile, this script is copied to that directory so that permissions are changed for us on startup.
#   This is only possible because the nginx user runs as root

# If you run this the command above directly in the Dockerfile it won't work
#   because the image doesnt have access to the host container filesystem at build time.
#   Also, when the container is mounted the host permissions masks/overwrites the container's ones

# Alternatively, you can follow the steps in this article https://jtreminio.com/blog/running-docker-containers-as-current-host-user/#ok-so-what-actually-works
#   and create a www-user with the host id and gid for BOTH php and nginx in the Dockerfile.
#   Instead of using the long command in the article it should be sufficient to just add these lines to the Dockerfile
#   for both php and nginx build commands:
#       RUN groupmod -g <host group id> www-data
#       RUN usermod -u <host user id> www-data
