<?php namespace App\Helpers\Async;

class SendEmail extends \Thread{
    public function __construct($query){
        $this->query = $query;
    }

    public function run(){
        $testContent = file_get_contents('http://google.fr?q='.$query);
        $testContent = file_get_contents('http://google.fr?q='.$query);
    }
}