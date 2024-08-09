#!/bin/sh
set -e

git pull

docker compose -f docker-compose.prod.yml run --rm -it composer install

docker compose -f docker-compose.prod.yml run --rm -it --user node npm install

docker compose -f docker-compose.prod.yml run --rm -it --user node npm run build-no-tsc
