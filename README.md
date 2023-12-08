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
To get list of books you must send header X-AUTH-TOKEN that you received after login
```
  http://127.0.0.1:8000/api/books
```
#### GET: Single Book
To get single book specify the book id on the request and authenticate. In this case, the book id is specified with 12.
```
  http://127.0.0.1:8000/api/book/12
```
#### PATCH: Update Book
To update a book specify the book id on the request and authenticate.
```
  http://127.0.0.1:8000/api/book/update/12
```

## Development server

Run symfony server:start for a dev server. Navigate to `http://127.0.0.1:8000/`.
