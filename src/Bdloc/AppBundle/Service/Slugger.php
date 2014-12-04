<?php
    namespace Bdloc\AppBundle\Services;

    class slugger {

        private $book;
        public $yo;

        public function_construct($doctrine){
            $this->doctrine = $doctrine;
        }


        public function setBook($book){
            $this->book = $book;
        }
        public function test() {
        die('test');
        }

        public function sluggify($string){
            $slug )= strtolower($string);
            return $slug;
        }

    }
    