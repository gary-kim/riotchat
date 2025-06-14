app_name=$(notdir $(CURDIR))
build_tools_directory=$(CURDIR)/build/tools
source_build_directory=$(CURDIR)/build/artifacts/source
source_package_name=$(source_build_directory)/$(app_name)
appstore_build_directory=$(CURDIR)/build/artifacts/appstore
appstore_package_name=$(appstore_build_directory)/$(app_name)
npm?=$(shell which npm 2> /dev/null)
node?=$(shell which node 2> /dev/null)
SHELL = /bin/bash

all: dev-setup build

# Set up the dev environment
.PHONY: dev-setup
dev-setup: 3rdparty/riot
	npm i

3rdparty/riot: 3rdparty/riot-web
	(cp ./src/MessageComposerButtons.tsx 3rdparty/riot-web/src/components/views/rooms/MessageComposerButtons.tsx && cp ./css/_MessageComposer.pcss 3rdparty/riot-web/res/css/views/rooms/_MessageComposer.pcss && cd 3rdparty/riot-web && rm -rf ../riot && yarn && yarn add @nextcloud/browserslist-config --ignore-engines && (VERSION=$$(git describe --abbrev=0 --tags) yarn build) && cp config.sample.json webapp/ && cp element.io/develop/config.json webapp/develop.config.json && git describe --abbrev=0 --tags | cut -c 2- > webapp/version && mv webapp ../riot && yarn remove @nextcloud/browserslist-config) || (cd 3rdparty/riot-web && yarn remove @nextcloud/browserslist-config && exit 1)

.PHONY: build
build:
	npm run build

.PHONY: clean
clean:
	rm -rf ./build

.PHONY: distclean
distclean: clean
	rm -rf node_modules
	rm -rf js

.PHONY: dist
dist: appstore source

.PHONY: source
source:
	rm -rf $(source_build_directory)
	mkdir -p $(source_build_directory)
	tar cvzf $(source_package_name).tar.gz ../$(app_name) \
	--exclude-vcs \
	--exclude="../$(app_name)/build" \
	--exclude="../$(app_name)/node_modules" \

.PHONY: appstore
appstore:

