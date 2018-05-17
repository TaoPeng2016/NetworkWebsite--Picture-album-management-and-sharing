<?php
session_start();
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';

$_SESSION['attemptAccessPage'] = 'UploadPictures.php';
if (!isset($_SESSION['loginUser'])){
    
    header("location: Login.php");
    exit();
}
$uploadError = $titleError = "";
$albumValue = $descriptionValue = $titleValue = "";
$loginUser = unserialize($_SESSION['loginUser']);
$userName = $loginUser->getName();
$userId = $loginUser->getId();

extract($_POST);

if (isset($btnSubmit) && ($_SESSION['key'] == $hiddenKey)){
    $titleValidateSuccess = ValidateTitle($imgTitle);
    if (!$titleValidateSuccess) {
        $titleError = "Title can not be blank";
        $descriptionValue = $description;
        $albumValue = $selectedAlbum;
    }
    else{
            $success = true;
                          
            for ($i = 0; $i < count($_FILES['fileUpload']['tmp_name']); $i++) {

                if ($_FILES['fileUpload']['error'][$i] == 0) {
                   
                    $tempFilePath = $_FILES['fileUpload']['tmp_name'][$i];
                    $filePath = $_FILES['fileUpload']['name'][$i];
                    $uploadFile = new File($tempFilePath, $filePath);
                    AddNewPicture($selectedAlbum, $imgTitle, $description, $uploadFile);
                    
                }
                else {
                    if ($_FILES['fileUpload']['error'][$i] == 1) {
                        $fileName = $_FILES['fileUpload']['name'][$i];
                        $uploadError = "$fileName is too large";
                        
                    }
                    else if ($_FILES['fileUpload']['error'][$i] == 4) {
                        $uploadError = "No upload file specified";
                    }
                        $titleValue = $imgTitle;
                        $descriptionValue = $description;
                        $albumValue = $selectedAlbum;
                        $success = false;
                        //echo "<script>$('body').waitMe('hide')</script>";
                }
            }
    }

    if ($success) {
         //echo '<script>$("body").waitMe("hide")</script>';
         echo "<script>ShowDiaglogBox('Upload Picture(s) successfully')</script>";
         $albumValue = $descriptionValue = $titleValue = "";
    }
}
$myAlbums =  GetMyAlbum($userId); 
$_SESSION['key'] = mt_rand(1, 100000);
?>

<div id="uploadPicture" class = "upload-picture">
    <h1 class = "new-album-title">Upload Pictures</h1>
    <p class="label-padding">accepted picture types: JPG(JEPG), GIF and PNG.</p>
    <p class="label-padding">You can upload multiple pictures at a time by pressing the shift key while selecting pictures.</p>
    <p class="label-padding">When uploading multiple pictures, the title and description fields will be applied to all pictures</p>
    
    <form action = "UploadPictures.php" role="form" method = "post" enctype="multipart/form-data">
        <input type="hidden" name="hiddenKey" value ="<?php echo $_SESSION['key']?>" />
         <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Upload to Album:</div>
            <div class = "col-sm-4">
                <select class="form-control" name="selectedAlbum">
                    <?php 
                    foreach ($myAlbums as $eachAlbum){
                        $id = $eachAlbum->getAlbumId();
                        $title = $eachAlbum->getTitle();
                        echo "<option value = '$id'>$title</option>";
                    }
                    ?>
                </select>
            </div>
           
        </div>
        <div class="row horizontal-margin vertical-margin">
            <div class="col-sm-2 label-padding highlight label-length">File to Upload</div>
            <div class="col-sm-4">
                <input type="file" class="form-control" name="fileUpload[]" multiple accept="image/*"/>
            </div>
             <div class="error col-sm-4"><?php echo $uploadError ?></div>
        </div>
        <div class="row horizontal-margin vertical-margin">
            <div class="col-sm-2 label-padding highlight label-length">Title</div>
            <div class="col-sm-4">
                <input type="text" class = "form-control" name="imgTitle" value="<?php echo $titleValue ?>" />
            </div>
             <div class="error col-sm-4"><?php echo $titleError ?></div>
        </div>
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Description:</div>
            <div class = "col-sm-4">
                <textarea  class = "form-control" rows= "5" name = "description" ><?php echo "$descriptionValue" ?></textarea>
            </div>
        </div>  
        <div class="row  v-margin label-padding">
            <div class="col-sm-1 btn-submit "><button type = "submit" name = "btnSubmit" class = "btn btn-success btn-block" onclick="run_waitMe()">Submit</button></div>
            <div class="col-sm-1 btn-clear "><button type ="submit" class = "btn btn-warning btn-block" name = "btnclear">Clear</button></div>
        </div>
      
</div>
<?php include 'ProjectCommon/Footer.php' ?>
