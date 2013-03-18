PHPDOC=phpdoc.bat
PHPUNIT=phpunit.bat

SOURCES=index.php admin.php classes/model.php
RIC=README CHANGELOG

EMPTY=
SPACE=$(EMPTY) $(EMPTY)
COMMA=,


all: doc test

doc: $(SOURCES) $(RIC)
	$(PHPDOC) --parseprivate -f $(subst $(SPACE),$(COMMA),$(SOURCES) $(RIC)) -t doc


.PHONY: test
test: $(SOURCES)
	cd test; $(PHPUNIT) *Test.php; cd ..


.PHONY: clean
clean:
	rm -rf doc test/data
