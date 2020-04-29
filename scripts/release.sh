#!/usr/bin/env bash

set -euf -o pipefail

SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

rm -r "$SCRIPTPATH/../build" || true

(cd "$SCRIPTPATH/../" && make)

cd "$SCRIPTPATH"

TMPDIR="$(mktemp -d)"

cp -r "../" "$TMPDIR/riotchat"

cd "$TMPDIR/riotchat"

git add -Af

rm -rf $(git ls-files -i --exclude-from=.nextcloudignore) build .git .git*

find "$TMPDIR/riotchat" -empty -type d -delete

mkdir -p "$SCRIPTPATH/../build/artifacts/"

mv "$TMPDIR/riotchat" "$SCRIPTPATH/../build/artifacts/riotchat"
