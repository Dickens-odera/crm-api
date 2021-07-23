# Agile Monkeys API Backend
---

### Overview
This is a simple REST API that purposes to power the Agile Monkeys CRM service<br> 
Key Modules Implemented include:
- Authentication using [sanctum](https://laravel.com/docs/8.x/sanctum) for issuing API tokens
- Authorization using [spatie laravel permissions package](https://spatie.be/docs/laravel-permission/v4/introduction) and access control
- Role Management
- User Management
- Customer Management

### Demo URL
Please find the [demo](https://www.agilebackend.tujiajiriafrica.com/docs) url here.

### Installation
first clone the repository
```php
git clone https://github.com/Dickens-odera/crm-api
```
Change the directory to the clone path
```php
cd crm-api
```
then install the packages with composer
```php
composer install
```

Make a copy of the .env.example file and create a database in your db server
```php
cp .env.example .env
```
<p>At the Created .env file add the following:</p>
DB_USERNAME=root<br>
DB_DATABASE=<your_database_name></br>
DB_PASSWORD=<your_db_password>

Generate app key
```php
php artisan key:generate
```
Modify APP_URL at the .env file to server url i.e APP_URL=http://localhost:8000 so that scribe can pick this for the endpoints<br>

Generate API docs using the [scribe](https://scribe.readthedocs.io/en/latest/guide-getting-started.html#basic-configuration) API documentation and testing package
```php
php artisan scribe:generate
```
Run Database Migrations
```php
php artisan migrate --seed
```
This creates an admin user with the following credentials
- Email: admin@agilemonkeys.com
- Password: admin123

### How It Works
After seeding the database in the above step, use the above credentials to test the app on the [demo](https://www.agilebackend.tujiajiriafrica.com/docs) site or on your local machine

#### Access Tokens
For Authentication, this API uses the lightweight Laravel [sanctum](https://laravel.com/docs/8.x/sanctum) package to issue personal access tokens to authenticate

##### Endpoints that do not require authentication
- Login
- Register

Otherwise, all other endpoints will require you to be authenticated, to do this use the credentials above:
- Visit the documentation page at */docs* e.g http://localhost:8000/docs
- Hit The Login endpoint under Auth -> User Login on the API docs
- Copy the **accessToken** generated and 
- Paste it as the **Authorization Header** for every endpoint you visit

