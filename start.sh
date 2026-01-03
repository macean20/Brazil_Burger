#!/bin/bash
set -e

php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

apache2-foreground

