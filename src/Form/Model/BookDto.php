<?php


namespace App\Form\Model;

class BookDto{
    public $title;
    public $description;
    public $imageBase64;
    public $price;
    public $author;
    public $category;
    public $tags;

    public function __construct()
    {
        $this->tags = [];
    }
}