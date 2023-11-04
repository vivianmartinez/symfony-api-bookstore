<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddTags{

    private $em;
    private $tagRepository;

    public function __construct(EntityManagerInterface $em, TagRepository $tagRepository)
    {
        $this->em = $em;
        $this->tagRepository = $tagRepository;
    }

    public function addTagsBook($bookTags,$tagDto)
    {
        //get all current tags in book
        $current_tags_book = [];
        if(!empty($bookTags)){
            foreach($bookTags as $bookTag){
                array_push($current_tags_book,$bookTag->getId());
            }
        }
        
        $error = null;
        $addTag = null;
        //validate if send correct data
        if($tagDto->id == null && $tagDto->name == null){
            $error = ['error'=>true,'message'=>'You must enter the tag id to add a tag to book or send name to create a new tag'];
            
        }else if($tagDto->id !== null && $tagDto->name !== null){
            $error = ['error'=>true,'message'=>'Bad request. If tag exist send Id or create a new tag sending only name'];
        }
        // if send id verify if exists on Tag
        if($tagDto->id){
            $tag = $this->tagRepository->find($tagDto->id);
            if(!$tag){
                $error = ['error'=>true,'message'=>'Bad request. The tag with Id '.$tagDto->id.' not exists. Verify Id or create a new tag.'];
            }elseif(!in_array($tagDto->id,$current_tags_book)){
                //if id exists on Tag and not exists in book->getTags() we add the tag to the book
                $addTag = $tag;
            }
            
        }elseif($tagDto->name){   
            //verify if send name and that name doesn't exists on Tag
            $tag = $this->tagRepository->findOneByName($tagDto->name);
            //if name even exists return error
            if($tag){
                $error = ['error'=>true,'message'=>'The tag even exists on Tags. Assign it to your book sending id '. $tag['id']];
            }
            //if there is no error we create the tag
            $newTag = new Tag();
            $newTag->setName($tagDto->name);
            $this->em->persist($newTag);
            $this->em->flush($newTag);
            $addTag = $newTag;
        }

        return ['error'=>$error, 'add_tag' => $addTag];
    }
}