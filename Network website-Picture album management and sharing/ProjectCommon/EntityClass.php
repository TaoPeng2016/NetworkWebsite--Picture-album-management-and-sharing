<?php

class User{
    private $id;
    private $name;
    private $phone;
    
    public function __construct($id, $name, $phone){
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->courses = array();
    }
   
    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getPhone() {
        return $this->phone;
    }
}
class Album{
    private $id;
    private $title;
    private $updateDate;
    private $numberOfPictures;
    private $code;    
    private $owenId;


    public function __construct($id, $title, $updateDate, $owenId, $code) {
        $this->id = $id;
        $this->title = $title;
        $this->updateDate = $updateDate;
        $this->owenId = $owenId;
        $this->code = $code;
    }
    
    public function setPictureNumbers($pictureNumber){
        $this->numberOfPictures = $pictureNumber;
    }
    
     public function getAlbumId(){
        return $this->id;
    }
    public function getTitle(){
        return $this->title;
    }
    
    public function getDate(){
        return $this->updateDate;
    }
    
    public  function getOwenId(){
        return $this->owenId;
    }
    public function getCode(){
        return $this->code;
    }

    public function getNumberOfPictures(){
        return $this->numberOfPictures;
    }
}

class Picture {
    private $fileName;
    private $id;
    private $album_Id;
    private $date_Updated;
    private $title;
    private $description;
    
    public function __construct($id, $title, $album_Id, $date_Updated, $description, $fileName) {
        $this->fileName = $fileName;
        $this->id = $id;
        $this->album_Id = $album_Id;
        $this->date_Updated = $date_Updated;
        $this->title = $title;
        $this->description = $description;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function getDateUpdated(){
        return $this->date_Updated;
    }
    public function getFileName(){
        return $this->fileName;
    }
    public function getAlbumId() {
        return $this->album_Id;
    }
    /* public function getName() {
        $ind = strripos($this->fileName, '.');
        $name = substr($this->fileName, 0, $ind);
        return $name;
    }*/
 public function getTitle(){
     return $this->title;
 }

  public function getAlbumFilePath() {
        return ALBUM_PICTURES_DIR."/".$this->fileName;
    }
    
    public function getThumbnailFilePath() {
        return ALBUM_THUMBNAILS_DIR."/".$this->fileName;
    }
    
    public  function getOriginalFilePath() {
        return ORIGINAL_PICTURES_DIR."/".$this->fileName;
    }
}

class Accessibility {
    private $code;
    private $description;
    
    public function __construct($code, $description) {
        $this->code = $code;
        $this->description = $description;
    }
    public function getCode(){
        return $this->code;
    }
    public function getDescription(){
        return $this->description;
    }
}

class File{
    private $tempFilePath;
    private $filePath;
 
    public function __construct($tempFilePath, $filePath) {
        $this->tempFilePath = $tempFilePath;
        $this->filePath = $filePath;
    
    }
    public function getFileTempPath(){
        return $this->tempFilePath;
    }
    public function getFilePath(){
        return $this->filePath;
    }

}

Class FriendShip {
    private $requesterId;
    private $requesteeId;
    private $status;
    
    public function __construct($requesterId, $requesteeId, $status) {
        $this->requesterId = $requesterId;
        $this->requesteeId = $requesteeId;
        $this->status = $status;
    }
    
    public function getRequesterId(){
        return $this->requesterId;
    }
    
    public function getRequesteeId(){
        return $this->requesteeId;
    }
    
    public function getStatus(){
        return $this->status;
    }
}

class Comment {
    private $id;
    private $authorId;
    private $pictureId;
    private $text;
    private $date;
    
    public function __construct($id, $authorId, $pictureId, $text, $date) {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->pictureId = $pictureId;
        $this->text = $text;
        $this->date = $date;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getAuthorId(){
        return $this->authorId;
    }
    
    public function getPictureId(){
        return $this->pictureId;
    }
    public function getCommentText(){
        return $this->text;
    }
    public function getDate(){
        return $this->date;
    }
}
