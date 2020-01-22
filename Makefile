
# Variables
SHELL := /bin/bash

## Applications
COMPOSER ?= composer
BOX ?= box
BIN ?= /home/akitson/Projects/cli/semantic-version-updater/bin/vupdate
GIT ?= git

# Helpers
all: build push

.PHONY: all

# Dependencies
depend:
	${GIT} checkout master
	${COMPOSER} install --no-dev

.PHONY: depend

# Building
build:
	${BIN}
	${BOX} compile
	chmod a+x bin/pumldbconv

push:
	${GIT} add --all
	${GIT} commit -m"Build"
	${GIT} tag $$(cat VERSION)
	${GIT} push origin master --tags

.PHONY: build push

# Cleaning
clean:
	rm -rf vendor
	rm bin/pumldbconv
	rm composer.lock

.PHONY: clean