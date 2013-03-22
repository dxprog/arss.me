#!/bin/bash
rm handlebars/*.js
for f in handlebars/*.handlebars
do
	handlebars $f -f $f.js
done
cat handlebars/*.js > templates.js
rm handlebars/*.js
