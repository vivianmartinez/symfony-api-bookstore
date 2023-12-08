# Symfony API Rest Bookstore  FOSRestBundle

I'm currently working on this project... 

Project generated with [Symfony CLI](https://symfony.com/download) version 6.3.7 to create API with [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle).

This API let you manage a Bookstore: Get information about books, authors, categories, assign tags. The user must be authorized to make the API call with an Authentication Token.

## Install

```
  $ symfony new my_project
  $ composer require symfony/orm-pack
  $ composer require --dev symfony/maker-bundle
  $ composer require symfony/serializer
  $ composer require friendsofsymfony/rest-bundle
  $ composer require symfony/validator
  $ composer require form
```
## Documentation

#### POST: Register user

To register user, send json with username and password
```
  http://127.0.0.1:8000/api/user/register
```
#### POST: Login

When user log receive an Authentication Token for secure access
```
  http://127.0.0.1:8000/api/login
```
#### GET: Books
Get list of books. You must send header X-AUTH-TOKEN that you receive after login
```
  http://127.0.0.1:8000/api/books
```


## Development server

Run symfony server:start for a dev server. Navigate to `http://127.0.0.1:8000/`.
