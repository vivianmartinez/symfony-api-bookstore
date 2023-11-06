<?php

namespace App\Form\Model;

use App\Validator\UniqueAuthor; //to use validator unique name author


class AuthorDto
{
    public $id;
    #[UniqueAuthor()]
    public $name;
}