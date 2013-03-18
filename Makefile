PHPDOC=phpdoc.bat

SOURCES=index.php admin.php classes/model.php
RIC=README CHANGELOG

EMPTY=
SPACE=$(EMPTY) $(EMPTY)
COMMA=,


all: doc

doc: $(SOURCES) $(RIC)
	$(PHPDOC) --parseprivate -f $(subst $(SPACE),$(COMMA),$(SOURCES) $(RIC)) -t doc


.PHONY: clean
clean:
	rm -rf doc
