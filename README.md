# eu-login-phpcas-wrapper

## Description

It is a simple test client built on top of phpCAS libray (https://github.com/apereo/phpCAS)
The purpose is to be able to do a simple login/logout test with several functionalities integrated:
1. Option to receive the entire list of user details from the auth server once authenticated
2. The option to accept only certains assurance levels
3. The option to to ask for certain authentication strengths when login (i.e use Password with Mobile confirmation )

## Requirements

* Composer
* PHP 5.6+
* Apache/Nginx

## Install

$ composer install


## Implemented methods for testing

* http://localhost/test.php - will login
* http://localhost/test.php?action=logout - will logout



