<?php
session_start();
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';


if (!isset($_SESSION['loginUser'])){
    $_SESSION['attemptAccessPage'] = 'MyAlbums.php';
    header("location: Login.php");
    exit();
}
$_SESSION['selectedAlbumId'] = NULL;
$loginUser = unserialize($_SESSION['loginUser']);
$userName = $loginUser->getName();
$userId = $loginUser->getId();
$myAlbums =  GetMyAlbum($userId);
if (urldecode($_GET["deleteId"]) != NULL) {
    $albumId = (int) urldecode($_GET["deleteId"]);
    DeleteAlbum($albumId, $userId);
    $_SESSION['selectedPictureId'] = NULL;
    header("location:MyAlbums.php");
    
}
extract($_POST);
if (isset($btnSaveChange)){
    for ($i = 0; $i < sizeof($myAlbums); $i++){
        $albumId = $myAlbums[$i]->getAlbumId();
        $code = $myAlbums[$i]->getCode();
        $name = "accessbility".$albumId;
        
        if ($_POST[$name] != $code){
            UpdateAccessbilityCode($albumId, $_POST[$name]);
             $_SESSION['selectedPictureId'] = NULL;
            header("location:MyAlbums.php");
            exit();
        }
    }
    
}

for($i = 0; $i < sizeof($myAlbums); $i++){
    $albumId = $myAlbums[$i]->getAlbumId();
    $numberOfPictures = getNumberOfPicturesForAlbum($albumId);
    $myAlbums[$i]->setPictureNumbers($numberOfPictures);
}
$accessibility = getAccessCodeFromAccessibility();
?>
<div class = "my-album">
    <h1 class="center v-margin">My Albums</h1>
    <p>Welcome <span class="highlight"><?php echo "$userName" ?>!</span>(not you? change user <a href="Login.php">here</a>)</p>
    <div class = "link-NewAlbum"><a href="AddAlbum.php">Create a New Album</a></div>

    <form action="MyAlbums.php" role="form" method="post">
        <div class="album-table">
              <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="col-md-3">Title</th><th scope="col" class="col-md-1">Date Updated</th><th scope="col" class="col-md-1">Number of Pictures</th><th scope="col" class="col-md-2">Accessibility</th><th scope="col" class="col-md-1"></th>
                    </tr>
                </thead>
                <?php 
                for($i = 0; $i < sizeof($myAlbums); $i++) {
                    $id = $myAlbums[$i]->getAlbumId();
                    $title = $myAlbums[$i]->getTitle();
                    $date = $myAlbums[$i]->getDate();
                    $picturesNumber = $myAlbums[$i]->getNumberOfPictures();
                    $accessCode = $myAlbums[$i]->getCode();
                    $selectItem = $accessCode == "private"? 0:1;
                    $deleteLink = "MyAlbums.php?deleteId=".$id;
                    $pictureLink = 'MyPictures.php?albumId='.$id;
                    $name = "accessbility".$id;
                    echo "<tr>";
                    echo "<td><a href='$pictureLink'>$title</a></td><td>$date</td><td>$picturesNumber</td>";
                    echo "<td><select class = 'form-control' name = '$name'>";
                    for($j = 0; $j < sizeof($accessibility); $j++){
                        $accessibilityCode = $accessibility[$j]->getCode();
                        $accessibilityDescription = $accessibility[$j]->getDescription();
                        if ($accessibilityCode == $accessCode) {
                            echo "<option value = '$accessibilityCode' selected = 'selected'>$accessibilityDescription</option>";
                        }
                        else {
                            echo "<option value = '$accessibilityCode'>$accessibilityDescription</option>";
                        }
                    }

                    echo "</select></td>";
                    echo "<td><a class='deleteAlbum' href='$deleteLink'>delete</a></td>";
                    echo "</tr>";
                }
                ?>

            </table>
        </div>
       
  
    <div class = "col-sm-2 album-save v-margin"><button type = "submit" name = "btnSaveChange" class = "btn btn-success btn-block">Save Changes</button></div>
</form>
</div>
<?php include 'ProjectCommon/Footer.php' ?>
