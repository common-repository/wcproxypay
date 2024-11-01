PANDOCSRCDIR=$(shell pwd)/doc
PANDOCOUTDIR=$(shell pwd)/doc

.PHONY: pandoc pandoc-clean

${PANDOCOUTDIR}/doc.html: ${PANDOCSRCDIR}/doc.md ${PANDOCOUTDIR}/style.css
	cd ${PANDOCSRCDIR} && pandoc -s -c style.css -o ${PANDOCOUTDIR}/doc.html doc.md

pandoc: ${PANDOCOUTDIR}/doc.html
doc: pandoc

pandoc-help:
	@echo "- pandoc (< doc): run pandoc to generate documentation."
help: pandoc-help
