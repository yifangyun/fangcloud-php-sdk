help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test           to perform unit tests.  Provide TEST to perform a specific test."
	@echo "  coverage       to perform unit tests with code coverage. Provide TEST to perform a specific test."
	@echo "  coverage-show  to show the code coverage report"
	@echo "  clean          to remove build artifacts"
	@echo "  docs           to build the Sphinx docs"
	@echo "  docs-show      to view the Sphinx docs"
	@echo "  tag            to modify the version, update changelog, and chag tag"
	@echo "  package        to build the phar and zip files"



clean:
	rm -rf artifacts/*

docs:
	vendor/bin/phpdoc run -d ./src -t --target=build_doc --cache-folder=build/phpdoc-cache

docs-show:
	open build_doc/index.html

tag:
	$(if $(TAG),,$(error TAG is not defined. Pass via "make tag TAG=4.2.1"))
	@echo Tagging $(TAG)
	chag update $(TAG)
	sed -i '' -e "s/VERSION = '.*'/VERSION = '$(TAG)'/" src/ClientInterface.php
	php -l src/ClientInterface.php
	git add -A
	git commit -m '$(TAG) release'
	chag tag

package:
	php build/packager.php

.PHONY: docs burgomaster coverage-show view-coverage