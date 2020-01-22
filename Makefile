
# Variables
SHELL := /bin/bash

## Applications
COMPOSER ?= composer
BOX ?= box
BIN ?= bin/vupdate
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
	${BOX} compile
	chmod a+x bin/pumldbconv

push:
	${BIN}
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