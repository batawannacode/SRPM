#!/bin/bash
npx concurrently -c \
"#93c5fd,#c4b5fd,#fdba74,#32a852,#32a881,#327ba8,#f5f242" \
"php artisan serve" \
"npm run dev" \
"php artisan reverb:start" \
"php artisan queue:listen redis" \
"php artisan queue:listen redis --queue=ai-queue --rest=60 --tries=1" \
"php artisan queue:listen redis --queue=employer-notification-queue --tries=1" \
"php artisan queue:listen redis --queue=notify-applicants-queue --tries=1" \
"php artisan queue:listen redis --queue=admin-backup-queue --tries=1 --timeout=0" \
"php artisan queue:listen redis --queue=database --tries=1" \
"php artisan schedule:work" \
--names=""\
"          server           ,"\
"           vite            ,"\
"          reverb           ,"\
"          redis            ,"\
"         ai-queue          ,"\
"employer-notification-queue,"\
"  applicant-notification   ,"\
"     admin-backup-queue    ,"\
"         database          ,"\
"         schedule          ,"\
