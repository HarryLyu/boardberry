#!/bin/sh
rsync -rlvzt --no-p --no-o --no-g --delete --exclude="deploy.sh" --exclude="deploy_local.sh" --exclude=".git" --exclude=".idea" ./ "root@boardberry.me"