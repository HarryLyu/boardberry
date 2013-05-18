#!/bin/sh
composer.phar install
rsync -rlvzt --no-p --no-o --no-g --delete --exclude="deploy.sh" --exclude="deploy_local.sh" --exclude=".git" --exclude=".idea" ./ "vitkovskii@5.152.206.131:/var/www/boardberry/prod"