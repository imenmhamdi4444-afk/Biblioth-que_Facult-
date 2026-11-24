#!/usr/bin/env bash
set -Eeuo pipefail

echo "== Composer metadata =="
composer validate --no-check-publish

echo "== Production vulnerability audit =="
composer audit --locked --no-dev --abandoned=ignore

echo "== PHP syntax =="
find src -type f -name '*.php' -print0 | xargs -0 -r -n1 php -l

echo "== Symfony container =="
php bin/console lint:container --env=dev

echo "== Twig templates =="
php bin/console lint:twig templates

echo "All checks passed."
