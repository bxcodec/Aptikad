Aptikad (Aplikasi Izin Tidak Hadir Jam Akademik)
===============================

Aptikad Build on Yii2 Advance template. 

This application is  allow student to make a request from application. Then the application will send SMS to the Guardian Lecturer and Board Dorm. 

The Guardian Lecturer will approve the request both, by SMS or by opening the Application from browser.



DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    ....				 This application work only in backend.  So Frontend not so important.
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```
