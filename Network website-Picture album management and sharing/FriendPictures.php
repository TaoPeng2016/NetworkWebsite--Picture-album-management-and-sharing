<?php
session_start();
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: private");
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';

if (!isset($_SESSION['loginUser'])){
    header("location: Login.php");
    exit();
}

$friendId = NULL;
if (urldecode($_GET['friendId']) != NULL){
    $friendId = (int) urldecode($_GET['friendId']);
    $_SESSION['friendId'] = $friendId;
}
else if (isset($_SESSION['friendId'])){
    $friendId = $_SESSION['friendId'];
}
else {
    header("Loction: MyFriends.php");
    exit();
}
$friend = GetUserById($friendId);
$friendName = $friend->getName();
// get user Id
$loginUser = unserialize($_SESSION['loginUser']);
$userId = $loginUser->getId();

//check friendship
$isFriend = CheckFriendShip($userId, $friendId);
/*if (!$isFriend){
    header('location: Myfriends.php');
    exit();;
}*/
//get share albums
$sharedAlbums = GetUserSharedAlbum($friendId);
$selectedSharedAlbumId = NULL;
$pictures = NULL;
$selectedSharedPictureId = NULL;
$selectedSharedPicture = NULL;
$comments = NULL;

// get selected Album and its pictures
if (urldecode($_GET['albumId']) != NULL){
   $selectedSharedAlbumId = (int) urldecode($_GET['albumId']);
   $_SESSION['selectedSharedAlbumId'] = $selectedSharedAlbumId;
   $_SESSION['selectedSharedPictureId'] = NULL;
}
else if ($_SESSION['selectedSharedAlbumId'] != NULL){
    $selectedSharedAlbumId = (int) $_SESSION['selectedSharedAlbumId'];
}
else if($sharedAlbums != NULL) {
    $selectedSharedAlbumId = $sharedAlbums[0]->getAlbumId();
     $_SESSION['selectedSharedAlbumId'] = $selectedSharedAlbumId;
}
if ($selectedSharedAlbumId != NULL){
    $pictures = GetPicturesByAlbumId($selectedSharedAlbumId);
   
}

//var_dump($pictures);

// get selected picutre
if (urldecode($_GET['pictureId']) != NULL){
    $selectedSharedPictureId = (int) urldecode($_GET['pictureId']);
    $_SESSION['selectedSharedPictureId'] = $selectedSharedPictureId;
}
else if ($_SESSION['selectedSharedPictureId'] != NULL) {
    $selectedSharedPictureId = $_SESSION['selectedSharedPictureId'];
}
else if ($pictures != NULL) {
    $selectedSharedPictureId =  $pictures[0]->getId();
    $_SESSION['selectedSharedPictureId'] = $selectedSharedPictureId;
}
extract($_POST);
if (isset($btnSubmit) && $_SESSION['key'] == $hiddenKey){
   
    if ($selectedSharedPictureId != NULL && $commentInput != NULL){
        SaveComment($userId, $selectedSharedPictureId, $commentInput);
    }
}
if ($selectedSharedPictureId != NULL && $pictures != NULL){
    foreach ($pictures as $picture){
        if ($selectedSharedPictureId == $picture->getId()){
            $selectedSharedPicture = $picture;
        }
    }
}

 $comments = GetPictureComment($selectedSharedPictureId);
  
$selectedThumbnailName = NULL;
$selectedThumbnailPath = NULL;
if ($selectedSharedPicture != NULL){
    $selectedThumbnailName = $selectedSharedPicture->getTitle();
    $selectedThumbnailPath = $selectedSharedPicture->getAlbumFilePath();
}
$_SESSION['key'] = mt_rand(0, 1000000);
?>
<div class = "my-pictures">
    <h3 class="center"><?php echo "$friendName"."'s Pictures" ?></h3>
   
    <div class = "picture-area">
        <div class = "album-list">
            <select id = "selectAlbum" class = "form-control" onchange = "changeShareAlbum()">
                <?php 
                    if ($sharedAlbums != NULL) {
                        foreach ($sharedAlbums as $myAlbum){
                            $albumId = $myAlbum->getAlbumId();
                            $albumName = $myAlbum->getTitle();
                            $updateDate = $myAlbum->getDate();
                            $optionContent = $albumName."- updated on ".$updateDate;
                            if ($albumId == $selectedSharedAlbumId){
                                echo "<option value = '$albumId' selected = 'selected'>$optionContent</option>";
                            }
                            else {
                                 echo "<option value = '$albumId'>$optionContent</option>";
                            }
                           
                        }
                    }
                ?>
            </select>
        </div>
        
        <div class = "picture-name"><h3><?php echo "$selectedThumbnailName" ?></h3></div>
        <div class = "img-container">
            <img src = "<?php echo $selectedThumbnailPath."?rnd=".rand(); ?>"/>
 
        </div>
        <?php 
            if ($pictures != NULL) {
                echo "<div class = 'thumbnail-list'>";
                for ($i = 0; $i < sizeof($pictures); $i++) {
                    $linkClass = "inselectedLink";
                    $pictureId = $pictures[$i]->getId();
                    if ( $pictureId == $selectedSharedPictureId) {
                        $linkClass = "selectedLink";
                    }
                    $link = "FriendPictures.php?pictureId=".$pictureId;
                    $imgPath =  $pictures[$i]->getThumbnailFilePath()."?rnd=".rand();
                    echo "<a href= $link class=$linkClass><img src= $imgPath></a>";
                }
                echo "</div>";
            }
        ?>
       
    </div>
    <div class = "text-area">
        <div class="comment-list">
            <p class="highlight">Description:</p>
            <?php 
                if ($selectedSharedPicture != NULL){
                     $description = $selectedSharedPicture->getDescription();
                    if ($description != NULL){
                        echo "<p>$description</p>";
                    }
                }
            ?>
            <br/>
            <p class="highlight">Comments:</p>
            <?php
                if ($comments != NULL){
                    for ($i = sizeof($comments) -1; $i >= 0; $i--){
                        $authorId = $comments[$i]->getAuthorId();
                        $author = GetUserById($authorId);
                        $authorName = $author->getName();
                        $date = $comments[$i]->getDate();
                        $important = $authorName." (".$date."):";
                        $commentText = $comments[$i]->getCommentText();
                        echo "<p><span class='important'>$important</span> $commentText</p>";
                        echo "<br/>";
                    }
                }
            ?>
        </div>
        <div class="write-comment">
            <form action="FriendPictures.php" role = "form" method="post">
                <input type="hidden" name="hiddenKey" value="<?php echo $_SESSION['key'] ?>"/>
                <?php
                    if ($selectedSharedPicture != NULL){
print <<<MAT
                        <div class="row col-sm-12"><textarea class="form-control" name="commentInput" rows="5" placeholder="Leave Comment" ></textarea></div>
                        <div class="row col-sm-6" style="margin-top: 10px" ><button type = "submit" name = "btnSubmit" class = "btn btn-primary btn-block">Add Comment</button></div>
MAT;
                    }
                ?>
                
            </form>
        </div>
    </div>
</div>
<?php include 'ProjectCommon/Footer.php' ?>
