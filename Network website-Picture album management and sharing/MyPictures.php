<?php
session_start();
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: private");
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';


if (!isset($_SESSION['loginUser'])){
    $_SESSION['attemptAccessPage'] = 'MyPictures.php';
    header("location: Login.php");
    exit();
}
extract($_POST);
$loginUser = unserialize($_SESSION['loginUser']);
$userName = $loginUser->getName();
$userId = $loginUser->getId();
$myAlbums =  GetMyAlbum($userId);
$selectedAlbumId = NULL;
$pictures = NULL;
$selectedPictureId = NULL;
$selectedAction = NULL;
$selectedPicture = NULL;
$comments = NULL;
// get selected Album and its pictures
if (urldecode($_GET['albumId']) != NULL){
   $selectedAlbumId = (int) urldecode($_GET['albumId']);
   $_SESSION['selectedAlbumId'] = $selectedAlbumId;
   $_SESSION['selectedPictureId'] = NULL;
}
else if ($_SESSION['selectedAlbumId'] != NULL){
    $selectedAlbumId = (int) $_SESSION['selectedAlbumId'];
}
else if($myAlbums != NULL) {
    $selectedAlbumId = $myAlbums[0]->getAlbumId();
}
if ($selectedAlbumId != NULL){
    $pictures = GetPicturesByAlbumId($selectedAlbumId);
}
//var_dump($selectedAlbumId);
//var_dump($pictures);

// get selected picutre
if (urldecode($_GET['pictureId']) != NULL){
    $selectedPictureId = (int) urldecode($_GET['pictureId']);
    $_SESSION['selectedPictureId'] = $selectedPictureId;
}
else if ($_SESSION['selectedPictureId'] != NULL) {
    $selectedPictureId = $_SESSION['selectedPictureId'];
}
else if ($pictures != NULL) {
    $selectedPictureId =  $pictures[0]->getId();
    $_SESSION['selectedPictureId'] = $selectedPictureId;
}

if (isset($btnSubmit) && $_SESSION['key'] == $hiddenKey){
   
    if ($selectedPictureId != NULL && $commentInput != NULL){
        SaveComment($userId, $selectedPictureId, $commentInput);
    }
}
if ($selectedPictureId != NULL && $pictures != NULL){
    foreach ($pictures as $picture){
        if ($selectedPictureId == $picture->getId()){
            $selectedPicture = $picture;
        }
    }
}

 $comments = GetPictureComment($selectedPictureId);
  

// get selected action
if (urldecode($_GET["selectedAction"]) != NULL) {
    $action = (string) urldecode($_GET["selectedAction"]);
    if ($pictures != NULL) {
        $selectedThumbnailPath = $selectedPicture->getThumbnailFilePath();
        $selectedThumbPicturePath = $selectedPicture->getAlbumFilePath();
        $selectedOriginalFilePath = $selectedPicture->getOriginalFilePath();
        if ($action == "rotateLeft") {

            rotateImage($selectedOriginalFilePath, 90);
            // save image as Album picture
            resamplePicture($selectedOriginalFilePath, ALBUM_PICTURES_DIR, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
            // save image as thumbnail
            resamplePicture($selectedOriginalFilePath, ALBUM_THUMBNAILS_DIR, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);

            header("location: MyPictures.php");
        }
        else if ($action == "rotateRight") {

            rotateImage($selectedOriginalFilePath, -90);
             // save image as Album picture
            resamplePicture($selectedOriginalFilePath, ALBUM_PICTURES_DIR, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
                    // save image as thumbnail
            resamplePicture($selectedOriginalFilePath, ALBUM_THUMBNAILS_DIR, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
            //$page = $_SERVER['PHP_SELF'];
            header("location: MyPictures.php");
            //header("Refresh:0; url=$page");
        }
        else if ($action == "download") {
            downloadFile($selectedOriginalFilePath);
        }
        else if ($action == "delete") {
            if ($selectedPicture != NULL){
                DeletePicture($selectedPicture);
                $_SESSION['selectedPictureId'] = NULL;
                header("location: MyPictures.php");
            }
          
        }
    }
}

$selectedThumbnailName = NULL;
$selectedThumbnailPath = NULL;
if ($selectedPicture != NULL){
    $selectedThumbnailName = $selectedPicture->getTitle();
    $selectedThumbnailPath = $selectedPicture->getAlbumFilePath();
}
$_SESSION['key'] = mt_rand(0, 1000000);
?>
<div class = "my-pictures">
    <h3 class="center">My Pictures</h3>
   
    <div class = "picture-area">
        <div class = "album-list">
            <select id = "selectAlbum" class = "form-control" onchange = "changeAlbum()">
                <?php 
                    if ($myAlbums != NULL) {
                        foreach ($myAlbums as $myAlbum){
                            $albumId = $myAlbum->getAlbumId();
                            $albumName = $myAlbum->getTitle();
                            $updateDate = $myAlbum->getDate();
                            $optionContent = $albumName."- updated on ".$updateDate;
                            if ($albumId == $selectedAlbumId){
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
             <?php 
        if ($selectedPicture != NULL) {
print <<<MAT
         
            <div id="actionList" style="position: absolute; left: 0; bottom: 0; width: 100%; text-align: center">
                <a  href='MyPictures.php?selectedAction=rotateLeft'><span class="glyphicon glyphicon-repeat gly-filp-horizontal"></span></a>
                <a  href="MyPictures.php?selectedAction=rotateRight"><span class="glyphicon glyphicon-repeat"></span></a>
                <a  href="MyPictures.php?selectedAction=download"><span class="glyphicon glyphicon-download-alt"></span></a>
                <a  href="MyPictures.php?selectedAction=delete"><span class="glyphicon glyphicon-trash"></span></a>
         </div>    
MAT;
        }
        ?>
        </div>
        <?php 
            if ($pictures != NULL) {
                echo "<div class = 'thumbnail-list'>";
                for ($i = 0; $i < sizeof($pictures); $i++) {
                    $linkClass = "inselectedLink";
                    $pictureId = $pictures[$i]->getId();
                    if ( $pictureId == $selectedPictureId) {
                        $linkClass = "selectedLink";
                    }
                    $link = "MyPictures.php?pictureId=".$pictureId;
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
                if ($selectedPicture != NULL){
                     $description = $selectedPicture->getDescription();
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
            <form action="MyPictures.php" role = "form" method="post">
                <input type="hidden" name="hiddenKey" value="<?php echo $_SESSION['key'] ?>"/>
                <?php
                    if ($selectedPicture != NULL){
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


