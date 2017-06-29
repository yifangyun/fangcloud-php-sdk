help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  clean          to remove build artifacts"
	@echo "  docs           to build the Sphinx docs"
	@echo "  docs-show      to view the Sphinx docs"
	@echo "  package        to package a phar and zip file for a release"
	@echo "  check-tag      to ensure that the TAG argument was passed"
	@echo "  tag            to chag tag a release based on the changelog. Must provide a TAG"
	@echo "  release        to package the release and push it to GitHub. Must provide a TAG"
	@echo "  full-release   to tag, package, and release the SDK. Provide TAG"



clean:
	rm -rf build/artifacts/*

docs:
	vendor/bin/phpdoc run -d ./src -t --target=build/artifacts/docs --cache-folder=build/artifacts/phpdoc-cache

docs-show:
	open build_doc/index.html

package:
	mkdir -p build/artifacts
	php build/packager.php

# Ensures that the TAG variable was passed to the make command
check-tag:
	$(if $(TAG),,$(error TAG is not defined. Pass via "make tag TAG=4.2.1"))

# Creates a release but does not push it. This task updates the changelog
# with the TAG environment variable, replaces the VERSION constant, ensures
# that the source is still valid after updating, commits the changelog and
# updated VERSION constant, creates an annotated git tag using chag, and
# prints out a diff of the last commit.
tag: check-tag
	@echo Tagging $(TAG)
	chag update $(TAG)
	sed -i'' -e "s/VERSION = '.*'/VERSION = '$(TAG)'/" src/YfyClient.php
	php -l src/YfyClient.php
	git commit -a -m '$(TAG) release'
	chag tag
	@echo "Release has been created. Push using 'make release'"
	@echo "Changes made in the release commit"
	git diff HEAD~1 HEAD

# Creates a release based on the master branch and latest tag. This task
# pushes the latest tag, pushes master, creates a phar and zip, and creates
# a Github release. Use "TAG=X.Y.Z make tag" to create a release, and use
# "make release" to push a release. This task requires that the
# OAUTH_TOKEN environment variable is available and the token has permission
# to push to the repository.
release: check-tag package
	git push origin master
	git push origin $(TAG)
	php build/gh-release.php $(TAG)

# Tags the repo and publishes a release.
full_release: tag release

.PHONY: clean docs docs-show package check-tag tag release full-release