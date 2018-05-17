<?php
include_once 'EntityClass.php';
include_once 'ConstantsAndSettings.php';

date_default_timezone_set("America/Toronto");
function ValidateUserId($id) {
    return $id != NULL? TRUE:FALSE;
}

function ValidateUserName($name){
   return $name !=NULL? TRUE:FALSE;
}

function ValidatePhone($phone) {

    $phoneRegExp = "/^[2-9][0-9][0-9]-[2-9][0-9][0-9]-[0-9]{4}$/";
    if (preg_match($phoneRegExp, $phone)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function ValidateTitle($title){
    return $title != NULL? TRUE:FALSE;
}

function ValidatePassword($password) {
    $passwordError = "";
    if ($password == NULL) {
        $passwordError = "blank error";
        return $passwordError;
    }
    if(strlen($password) < 6){
        $passwordError = "length error";
        return $passwordError;
    }
    $containsUpperCase = preg_match('/[A-Z]/', $password);
    $containsLowerCase = preg_match('/[a-z]/', $password);
    $containDigitNumber = preg_match('/\d/', $password);
    if (!$containsUpperCase || !$containsLowerCase || !$containDigitNumber){
        $passwordError = "format error";
    }
    return $passwordError;
}

function ValidatePasswordMatch($password1, $password2) {
    return $password1 == $password2 ? TRUE:FALSE;
}
// connect SQL server function, it returns a connection object
function ConnectSQLServer(){
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    return $myPdo;
}

// save a new user in User table
function SaveUserRecord($userId, $userName, $phoneNumber, $Password) {
    $userId = htmlentities($userId);  
    $userName = htmlentities($userName);
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM User WHERE UserId= :userId';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId'=>$userId]);
    foreach ($pStmt as $row) {
        if ($row['UserId'] != NULL) {
            return FALSE;
        }
    }
    $hashedPassword = sha1($Password);
    $insertStudent = "INSERT INTO User VALUES(:id, :name, :phoneNumber, :password)";
    $pSignUp = $myPdo->prepare($insertStudent);
    $pSignUp->execute(['id'=>$userId, 'name'=>$userName, 'phoneNumber'=>$phoneNumber, 'password'=>$hashedPassword]);
    return TRUE;
}

// user login function
function UserLogin($userId, $loginPassword) {
    $hashedLoginPassword = sha1($loginPassword);
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM User WHERE UserId= :userId AND Password= :password';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId'=>$userId, 'password'=>$hashedLoginPassword]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
     if ($row) {
          $loginUser = new User($row['UserId'], $row['Name'], $row['Phone']);
          return $loginUser;
     }
     else {
         return NULL;
     }
    
}

// get user by user id
function GetUserById($userId) {
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM User WHERE UserId = :userId';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId'=>$userId]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
     if ($row) {
          $user = new User($row['UserId'], $row['Name'], $row['Phone']);
          return $user;
     }
     else {
         return NULL;
     }
}
 // get all albums create by a user
function GetMyAlbum($userId) {
    $myAlbums = array();
    $myPdo = ConnectSQLServer();
    $getAlbumStatement = 'SELECT * FROM Album WHERE Owner_Id= :ownerId';
    $pStmt = $myPdo->prepare($getAlbumStatement);
    $pStmt->execute(['ownerId'=>$userId]);
    foreach ($pStmt as $row) {
        $album = new Album($row['Album_Id'], $row['Title'], $row['Date_Updated'], $row['Owner_Id'], $row['Accessibility_Code']);
        array_push($myAlbums, $album);
    }
    if (sizeof($myAlbums) != 0){
        return $myAlbums;
    }
    else {
        return NULL;
    }
}
  // get the number of pictures from an album
function getNumberOfPicturesForAlbum($albumId) {
    $myPdo = ConnectSQLServer();
    $getPicutresNumberStatement = 'SELECT COUNT(Album_Id) AS NumberOfPictures FROM Picture WHERE Album_Id = :albumId';
    $pStmt = $myPdo->prepare($getPicutresNumberStatement);
    $pStmt->execute(['albumId'=>$albumId]);
    foreach ($pStmt as $row){
        if ($row['NumberOfPictures'] != NULL){
            return $row['NumberOfPictures'];
        }
    }
}

// update accessbility code in an album
function UpdateAccessbilityCode($albumId, $code) {
    $myPdo = ConnectSQLServer();
    $updateAccessbilityStatement = 'UPDATE Album SET Accessibility_Code= :code WHERE Album_Id= :albumId';
    $pStmt = $myPdo->prepare($updateAccessbilityStatement);
    $pStmt->execute(['code'=>$code, 'albumId'=>$albumId]);
}

function GetPictureFileNameByAlbum($albumId){
    $fileNames = array();
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT FileName FROM Picture WHERE Album_Id = :albumId';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['albumId'=> $albumId]);
    foreach ($pStmt as $row){
        if ($row){
            array_push($fileNames, $row['FileName']);
        }
    }
    if (sizeof($fileNames) > 0){
        return $fileNames;
    }
    else {
        return NULL;
    }
}
function DeletePictureComments($pictureId){
    if ($pictureId == NULL) {
        return NULL;
    }
     $myPdo = ConnectSQLServer();
     $sqlStatement = 'DELETE FROM Comment WHERE Picture_Id = :pictureId';
     $pStmt = $myPdo->prepare($sqlStatement);
     $pStmt->execute(['pictureId'=>$pictureId]);
     
}
function DeletePicture($picture){
    $fileName = $picture->getFileName();
    $pictureId = $picture->getId();
    DeletePictureComments($pictureId);
    if (DeletePictureByName($fileName)){
        $myPdo = ConnectSQLServer();
        $sqlStatement = 'DELETE FROM Picture WHERE Picture_Id = :pictureId';
        $pStmt = $myPdo->prepare($sqlStatement);
        $pStmt->execute(['pictureId'=>$pictureId]);
    }
}

function DeletePictureByName($fileName){
   $albumPath = ALBUM_PICTURES_DIR."/".$fileName;
   $thumbnailPath = ALBUM_THUMBNAILS_DIR."/".$fileName;
   $originalPath = ORIGINAL_PICTURES_DIR."/".$fileName;
   if (unlink($originalPath) && unlink($thumbnailPath) && unlink($albumPath)){
       return true;
   }
   else {
       return false;
   }
}
// delete pictures belong to an album
function DeleteAlbumPictures($albumId) {
   /* $fileNames = GetPictureFileNameByAlbum($albumId);
    if ($fileNames != NULL){
        foreach ($fileNames as $fileName){
            DeletePictureByName($fileName);
        }
    }*/
    $albumPictures = GetPicturesByAlbumId($albumId);
    for ($i = 0; $i < sizeof($albumPictures); $i++){
        DeletePicture($albumPictures[$i]);
    }
    $myPdo = ConnectSQLServer();
    $deleteAlbumPicturesStatement = 'DELETE FROM Picture WHERE Album_Id = :albumId';
    $pStmt = $myPdo->prepare($deleteAlbumPicturesStatement);
    $pStmt->execute(['albumId'=>$albumId]);
    
}

// delete a specified album
function DeleteAlbum($albumId, $ownerId) {
    DeleteAlbumPictures($albumId);
    $myPdo = ConnectSQLServer();
    $removeAlbumStatement = 'DELETE FROM Album WHERE Album_Id = :albumId AND Owner_Id = :ownerId';
    $pStmt = $myPdo->prepare($removeAlbumStatement);
    $pStmt->execute(['albumId'=>$albumId, 'ownerId'=>$ownerId]);
}

// get an album by album id
function GetAlbumByAlbumId($albumId){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Album WHERE Album_Id = :albumId';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['albumId'=>$albumId]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if($row){
        $album = new Album($row['Album_Id'], $row['Title'], $row['Date_Updated'], $row['Owner_Id'], $row['accessibility_Code']);
        return $album;
    }
    else{
        return NULL;
    }
}

// get accessbility codes from Accessibility table
function getAccessCodeFromAccessibility(){
    $codes = array();
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Accessibility';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute();
    foreach($pStmt as $row) {
        $code = new Accessibility($row['Accessibility_Code'], $row['Description']);
        array_push($codes, $code);
    }
    if (sizeof($codes) != 0){
        return $codes;
    }
    else {
        return NULL;
    }
}


// add a new Album to database
function addNewAlbum($title, $description, $ownerId, $accessCode){
    $title = htmlentities($title);
    $description = htmlentities($description);
    $updateDate = date("Y-m-d");
    $myPdo = ConnectSQLServer();
    $addNewAlbumStatement = 'INSERT INTO Album(Title, Description, Date_Updated, Owner_Id, Accessibility_Code) VALUES (:title, :description, :dateUpdated, :ownerId, :accessCode)';
    $pStmt = $myPdo->prepare($addNewAlbumStatement);
    $pStmt->execute(['title'=>$title, 'description'=>$description, 'dateUpdated'=>$updateDate, 'ownerId'=>$ownerId, 'accessCode'=>$accessCode]);
}

// get pictures by album id
function GetPicturesByAlbumId($albumId){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Picture WHERE Album_Id = :albumId';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['albumId'=>$albumId]);
    $pictures = array();
    foreach($pStmt as $row){
        $picture = new Picture($row['Picture_Id'], $row['Title'], $row['Album_Id'], $row['Date_Added'], $row['Description'], $row['FileName']);
        array_push($pictures, $picture);
    }
    if (sizeof($pictures) > 0){
        return $pictures;
    }
    else {
        return NULL;
    }
}


function save_uploaded_file($destinationPath, $tempFilePath, $upLoadFilePath) {
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath);
    }
    //$tempFilePath = $_FILES['picUpload']['tmp_name'][$id];
    //$filePath = $destinationPath."/".$_FILES['picUpload']['name'][$id];
    $filePath = $destinationPath."/".$upLoadFilePath;
    $pathInfo = pathinfo($filePath);
    $dir = $pathInfo['dirname'];
    $fileName = $pathInfo['filename'];
    $ext = $pathInfo['extension'];
    
    //make sure not to overwrite existing file
    $i = "";
    while (file_exists($filePath)) {
        $i++;
        $filePath = $dir."/".$fileName."_".$i.".".$ext;
    }
    move_uploaded_file($tempFilePath, $filePath);
    return $filePath;
}

function resamplePicture($filePath, $destinationPath, $maxWidth, $maxHeight) {
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath);
    }
    $imagedetails = getimagesize($filePath);
    
    $originalResource = null;
    if ($imagedetails[2] == IMAGETYPE_JPEG) {
        $originalResource = imagecreatefromjpeg($filePath);
    }
    else if ($imagedetails[2] == IMAGETYPE_PNG) {
        $originalResource = imagecreatefrompng($filePath);
    }
    else if ($imagedetails[2] == IMAGETYPE_GIF) {
        $originalResource = imagecreatefromgif($filePath);
    }
    $widthRatio = $imagedetails[0] / $maxWidth;
    $heightRatio = $imagedetails[1] / $maxHeight;
    $ratio = max($widthRatio, $heightRatio);
    
    $newWidth = $imagedetails[0] / $ratio;
    $newHeight = $imagedetails[1] / $ratio;
    
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    $success = imagecopyresampled($newImage, $originalResource, 0, 0, 0, 0, $newWidth, $newHeight, $imagedetails[0], $imagedetails[1]);
    
    if (!$success) {
        imagedestroy($newImage);
        imagedestroy($originalResource);
        return "";
    }
    $pathInfo = pathinfo($filePath);
    $newFilePath = $destinationPath."/".$pathInfo['filename'];
    if ($imagedetails[2] == IMAGETYPE_JPEG) {
        $newFilePath .= ".jpg";
        $success = imagejpeg($newImage, $newFilePath, 100);
    }
    else if ($imagedetails[2] == IMAGETYPE_PNG) {
        $newFilePath .= ".png";
        $success = imagepng($newImage, $newFilePath, 0);
    }
    else if ($imagedetails[2] == IMAGETYPE_GIF) {
        $newFilePath .= ".gif";
        $success = imagegif($newImage, $newFilePath);
    }
    
    imagedestroy($newImage);
    imagedestroy($originalResource);
    
    if (!$success) {
        return "";
    }
    else {
        return $newFilePath;
    }
}

function rotateImage($filePath, $degrees) {
    
   $imageDetails = getimagesize($filePath);
   
   $originalResource = null;
   if ($imageDetails[2] == IMAGETYPE_JPEG) {
       $originalResource = imagecreatefromjpeg($filePath);
   }
   else if ($imageDetails[2] == IMAGETYPE_PNG) {
       $originalResource = imagecreatefrompng($filePath);
   }
   else if ($imageDetails[2] == IMAGETYPE_GIF) {
       $originalResource = imagecreatefromgif($filePath);
   }
   
   $rotateResource = imagerotate($originalResource, $degrees, 0);
   
   if ($imageDetails[2] == IMAGETYPE_JPEG) {
       $success = imagejpeg($rotateResource, $filePath, 100);
   }
   else if ($imageDetails[2] == IMAGETYPE_PNG) {
       $success = imagepng($rotateResource, $filePath, 0);
   }
   else if ($imageDetails[2] == IMAGETYPE_GIF) {
       $success = imagegif($rotateResource, $filePath);
   }
   imagedestroy($originalResource);
   imagedestroy($rotateResource);
   
}
function downloadFile($filePath) {
    $fileName = basename($filePath);
    $fileLength = filesize($filePath);
    
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename = \"$fileName\"");
    header("Content-Length: $fileLength");
    header("Content-Description: File Transfer");
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: private");
    
    ob_clean();
    flush();
    readfile($filePath);
    flush();
}

// add new Picture to Album
function AddNewPicture($albumId, $title, $description, $file){
    date_default_timezone_set("America/Tornoto");
    $dateUpdate =  date("Y-m-d");
    $tempPath = $file->getFileTempPath();
    $uploadFilePath = $file->getFilePath();
    $saveFilePath = save_uploaded_file(ORIGINAL_PICTURES_DIR, $tempPath, $uploadFilePath);
    
     // save image as Album picture
    resamplePicture($saveFilePath, ALBUM_PICTURES_DIR, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
     // save image as thumbnail
    resamplePicture($saveFilePath, ALBUM_THUMBNAILS_DIR, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
    $pathInfo = pathinfo($saveFilePath);
    $name = $pathInfo['filename'];
    $ext = $pathInfo['extension'];
    $fileName = $name.'.'.$ext;
    $title = htmlentities($title);
    $description = htmlentities($description);
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'INSERT INTO Picture(Album_Id, FileName, Title, Description, Date_Added) Values (:id, :fileName, :title, :description, :date)';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['id'=>$albumId, 'fileName'=>$fileName, 'title'=>$title, 'description'=>$description, 'date'=>$dateUpdate]);
}

// get friendship
function GetFriendShip($userId1, $userId2) {
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Friendship WHERE (Friend_RequesterId = :userId1 && Friend_RequesteeId = :userId2) OR (Friend_RequesterId = :userId3 && Friend_RequesteeId = :userId4)';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId1'=>$userId1, 'userId2'=>$userId2, 'userId3'=>$userId2, 'userId4'=>$userId1]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
   
    if ($row){
        $friendship = new FriendShip($row['Friend_RequesterId'], $row['Friend_RequesteeId'], $row['Status']);
        return $friendship;
    }
    else {
        return NULL;
    }
}

//check if is friend
function CheckFriendShip($userId1, $userId2) {
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Friendship WHERE (Friend_RequesterId = :userId1 && Friend_RequesteeId = :userId2) OR (Friend_RequesterId = :userId2 && Friend_RequesteeId = :userId1) && Status = "accepted"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId1'=>$userId1, 'userId2'=>$userId2]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if ($row){
        return TRUE;
    }
    else {
        return FALSE;
    }
}
// add friendship between 2 users
function AddFriendShip($requesterId, $requesteeId){
    if ($requesteeId == $requesterId) {
        return MYSELF;
    }
    $requestee = GetUserById($requesteeId);
    if (!$requestee) {
        return NOUSER;
    }
    $friendship = GetFriendShip($requesterId, $requesteeId);
    
    if ($friendship) {
         $friendshipStatus = $friendship->getStatus();
        if ($friendshipStatus == "accepted") {
            return BEFRIENDALREADY;
         }
    
        $friend_requesterId = $friendship->getRequesterId();
        $Status = $friendship->getStatus();
        if ($friend_requesterId == $requesterId) {
            return REPEATREQUEST;
        }
    
        if ($friend_requesterId == $requesteeId && $Status == 'request' ) {
            $myPdo = ConnectSQLServer();
            $sqlStatement = 'UPDATE Friendship SET Status = "accepted" WHERE (Friend_RequesterId = :userId1 && Friend_RequesteeId = :userId2) OR (Friend_RequesterId = :userId2 && Friend_RequesteeId = :userId1)';
            $pStmt = $myPdo->prepare($sqlStatement);
            $pStmt->execute(['userId1'=>$requesterId, 'userId2'=>$requesteeId]);
            return SUCCESS;
        }
    }
   
    else{
        $myPdo = ConnectSQLServer();
        $sqlStatement = 'INSERT INTO Friendship VALUES (:requesterId, :requesteeId, :status)';
        $pStmt = $myPdo->prepare($sqlStatement);
        $pStmt->execute(['requesterId'=>$requesterId, 'requesteeId'=>$requesteeId, 'status'=> 'request']);
        return SEND;
    }
    
}

// find user's friends, it return 
function GetMyFriends($userId){
    $friends = array();
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Friendship WHERE (Friend_RequesterId = :userId || Friend_RequesteeId = :userId) && Status = "accepted"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId'=>$userId]);
    foreach ($pStmt as $row) {
        $firend = new FriendShip($row['Friend_RequesterId'], $row['Friend_RequesteeId'], $row['Status']);
        array_push($friends, $firend);
    }
    if (sizeof($friends) != 0) {
        return $friends;
    }
    else{
        return NULL;
    }
}

// get user's shared album
function GetUserSharedAlbum($userId){
    $sharedAlbums = array();
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Album WHERE Owner_Id = :userId && Accessibility_Code = "shared"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId'=>$userId]);
    foreach ($pStmt as $row){
        $sharedAlbum = new Album($row['Album_Id'], $row['Title'], $row['Date_Updated'], $row['Owner_Id'], $row['Accessibility_Code']);
        array_push($sharedAlbums, $sharedAlbum);
    }
    if (sizeof($sharedAlbums) != 0){
        return $sharedAlbums;
    }
    else{
        return NULL;
    }
}

// ger friend requests to the user
function GetFriendRequest($userId){
    $friendships = array();
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Friendship WHERE Friend_RequesteeId = :userId && Status = "request"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['userId'=>$userId]);
    foreach ($pStmt as $row){
        $friendship = new FriendShip($row['Friend_RequesterId'], $row['Friend_RequesteeId'], $row['Status']);
        array_push($friendships, $friendship);
    }
    if (sizeof($friendships) != 0){
       return $friendships;
    }
    else{
        return NULL;
    }
}

// accept friend request
function AcceptFriendRequest($requesterId, $requesteeId){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'UPDATE Friendship SET Status = "accepted" WHERE Friend_RequesterId = :requesterId && Friend_RequesteeId= :requesteeId && Status = "request"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['requesterId'=>$requesterId, 'requesteeId'=>$requesteeId]);
}

// deny friend request
function DenyFriendRequest($requesterId, $requesteeId){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'DELETE FROM Friendship WHERE Friend_RequesterId = :requesterId && Friend_RequesteeId= :requesteeId && Status = "request"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['requesterId'=>$requesterId, 'requesteeId'=>$requesteeId]);
}

// Defriend friendship
function DefriendFriendship($requesterId, $requesteeId){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'DELETE FROM Friendship WHERE (Friend_RequesterId = :requesterId && Friend_RequesteeId= :requesteeId) || (Friend_RequesterId = :requesteeId && Friend_RequesteeId = :requesterId) && Status = "accepted"';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['requesterId'=>$requesterId, 'requesteeId'=>$requesteeId]); 
}

// save comment
function SaveComment($authorId, $pictureId, $commentText) {
    $commentText = htmlentities($commentText);
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'INSERT INTO Comment(Author_Id, Picture_Id, Comment_Text) Values (:authorId, :pictureId, :commentText)';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['authorId'=>$authorId, 'pictureId'=>$pictureId, 'commentText'=>$commentText]);
}

function GetPictureComment($pictureId){
    $comments = array();
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Comment WHERE Picture_Id = :pictureId ORDER BY "Date timestamp" DESC';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['pictureId'=>$pictureId]);
    foreach ($pStmt as $row) {
        
        if ($row){
            $timestamp = strtotime($row['Date timestamp']);
            $date = date('Y-m-d', $timestamp);
            $comment = new Comment($row['Comment_Id'], $row['Author_Id'], $row['Picture_Id'], $row['Comment_Text'], $date);
            array_push($comments, $comment);
        }
       
    }
    if (sizeof($comments) > 0){
        
        return $comments;
    }
    else {
        return NULL;
    }
}

