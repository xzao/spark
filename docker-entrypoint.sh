#!/bin/sh
#
#   docker-entrypoint.sh
#
set -e
mkdir -p /etc/spark/sparks
chown -R www-data:www-data /etc/spark
chmod -R u+rwX /etc/spark
exec "$@"
