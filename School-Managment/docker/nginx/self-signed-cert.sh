#!/bin/sh
# Run by the nginx image before nginx starts.
# If docker/certs/ has no server.crt + server.key, creates a self-signed certificate for SITE_HOST.
# To use a real certificate, put server.crt and server.key in docker/certs/ and restart "web".
set -e

cert_dir=/etc/nginx/certs
cert="$cert_dir/server.crt"
key="$cert_dir/server.key"

if [ -s "$cert" ] && [ -s "$key" ]; then
    exit 0
fi

host="${SITE_HOST:-localhost}"
san="DNS:localhost,IP:127.0.0.1"
if [ "$host" != "localhost" ]; then
    case "$host" in
        *[!0-9.]*) san="DNS:$host,$san" ;;
        *)         san="IP:$host,$san" ;;
    esac
fi

echo "$0: no TLS certificate found, creating a self-signed one for $host"
mkdir -p "$cert_dir"
openssl req -x509 -nodes -newkey rsa:2048 -sha256 -days 825 \
    -subj "/CN=$host" \
    -addext "subjectAltName=$san" \
    -keyout "$key" -out "$cert"
chmod 600 "$key"
