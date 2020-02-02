# Blog

## Table of contents
 * [Run the project](#run-the-project)
    - [Build the environment](#build-the-environment)
    - [Connect to the container](#connect-to-the-container)
    - [Apply migrations](#apply-migrations)
    - [Apply fixures](#apply-fixtures)
    - [Start the server](#start-the-server)
    - [Connect to the interface](#connect-to-the-interface)
 * [Create migration](#create-migration)
 * [Clear cache](#clear-cache)
 * [Connect to database](#connect-to-database)
 * [Destroy the environment](#destroy-the-environment)

## Run the project

### Build the environment

```sh
vagrant up
```

### Connect to the container

```sh
vagrant ssh
```

### Apply migrations

To apply the migrations:

```sh
php bin/console doctrine:migrations:migrate
```

### Apply fixtures

To apply the fixtures:

```sh
php bin/console doctrine:fixtures:load
```

### Start the server

```sh
php -S 0.0.0.0:8000 -t public/
```

### Connect to the interface

In your browser:

```sh
http://localhost:8080/
```

## Create migration

Migrations files can be created after models modification:

```sh
php bin/console doctrine:migrations:diff
```

## Clear cache

Clear the Symfony cache might be required sometimes to see the latest modifications
(especially the design modifications).

```sh
php bin/console cache:clear
```
## Connect to database

```sh
mysql -h blog-nakaoni_db -u vagrant -p
```

Mot de passe : vagrant

## Destroy the environment

To destroy the vagrant environment

```sh
vagrant destroy
```
