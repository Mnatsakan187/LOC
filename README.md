Terminal commands

******* Setup php
php artisan serve --p 8000
ngrok http localhost:8001 --subdomain=ob1-josh-rw3

****** To kill port
sudo lsof -i tcp:3000
sudo kill -9 PID

****** Front end setup

cd resources/frontend
npm install (1 time)
ng build --base-href http://localhost:8001/frontend/ (whenever there is a change)

****** API documentation setup
php artisan l5-swagger:generate
url: https://ob1-josh-rw3.ngrok.io/api/documentation

*** Run unit tests
vendor/bin/phpunit
