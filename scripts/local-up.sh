#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
docker-compose up -d wordpress
for attempt in $(seq 1 30); do
  if docker-compose run --rm cli db check >/dev/null 2>&1; then break; fi
  sleep 2
done
if ! docker-compose run --rm cli core is-installed >/dev/null 2>&1; then
  if [ ! -f .env ]; then
    umask 077
    printf 'TC_LOCAL_ADMIN_PASSWORD=%s\n' "$(openssl rand -hex 18)" > .env
  fi
  source .env
  docker-compose run --rm cli core install --url=http://localhost:8129 --title=TechCloud --admin_user=techcloud-admin --admin_password="$TC_LOCAL_ADMIN_PASSWORD" --admin_email=local-admin@example.test --skip-email
fi
docker-compose run --rm cli plugin activate techcloud-content
docker-compose run --rm cli theme activate techcloud
if ! docker-compose run --rm cli option get tc_seeded >/dev/null 2>&1; then
  docker-compose run --rm cli eval-file /scripts/seed.php
fi
docker-compose run --rm cli eval-file /scripts/refine-v2.php
docker-compose run --rm cli eval-file /scripts/restore-v3.php
docker-compose run --rm cli eval-file /scripts/refine-contact.php
docker-compose run --rm cli eval-file /scripts/refine-services-cases.php
docker-compose run --rm cli eval-file /scripts/add-policy-pages.php
docker-compose run --rm cli eval-file /scripts/apply-client-brief.php
docker-compose run --rm cli eval-file /scripts/refine-brief-layout.php
printf 'Local preview: http://localhost:8129\nLocal WordPress: http://localhost:8129/wp-admin/\nAdmin username: techcloud-admin (password is in the ignored .env file)\n'
