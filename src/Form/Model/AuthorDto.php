<?php

namespace App\Form\Model;

use App\Validator\UniqueAuthor;


class AuthorDto
{
    public $id;
    #[UniqueAuthor()]
    public $name;
}