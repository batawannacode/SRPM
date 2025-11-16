#!/bin/bash

composer run pre-dev
npx concurrently -c \
"#93c5fd,#c4b5fd,#f5f242" \
"php artisan serve" \
"npm run dev" \
"php artisan schedule:work" \
--names=""\
"          server           ,"\
"           vite            ,"\
"         schedule          ,"\
