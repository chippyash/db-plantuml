
# Variables
SHELL := /bin/bash

## Applications
COMPOSER ?= composer
BOX ?= box
BIN ?= /home/akitson/Projects/cli/semantic-version-updater/bin/vupdate
GIT ?= git

# Helpers
all: version build push

.PHONY: all

# Dependencies
depend:
	${GIT} checkout master
	${COMPOSER} install --no-dev

.PHONY: depend

# Building
build:
	${BOX} compile
	chmod a+x bin/pumldbconv

version:
	${BIN}

push:
	${GIT} add --all
	${GIT} commit -m"Build"
	${GIT} tag $$(cat VERSION)
	${GIT} push origin master --tags

.PHONY: build version push

# Cleaning
clean:
	rm -rf vendor
	rm bin/pumldbconv
	rm composer.lock

.PHONY: clean