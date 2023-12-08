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
<img src="/readme-pictures/post-user.png" width="800">

#### POST: Login

When user log receive an Authentication Token for secure access.
```
  http://127.0.0.1:8000/api/login
```
<img src="/readme-pictures/post-login.png" width="800">

#### GET: Books
To get list of books you must send header X-AUTH-TOKEN that you received after login.
```
  http://127.0.0.1:8000/api/books
```
#### GET: Single Book
To get single book specify the book id on the request and authenticate. In this case, the book id is specified with 12.
```
  http://127.0.0.1:8000/api/book/12
```
#### PATCH: Update Book
To update a book specify the book id on the request and authenticate. You must send a json with modifications, you can add tags to the book or create the tags directly.
```
  http://127.0.0.1:8000/api/book/update/12
```
#### PATCH: Update Book - deleting tag 
If you want to delete a tag from a book you can make this request. You must send a json with the tags ids you want to delete.
```
  http://127.0.0.1:8000/api/book/12/delete/tags
```
#### DELETE: book
To delete a book specify the book id on the request and authenticate. 
```
  http://127.0.0.1:8000/api/book/delete/12
```

## Development server

Run symfony server:start for a dev server. Navigate to `http://127.0.0.1:8000/`.
