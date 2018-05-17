<?php
session_start();
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';
extract($_POST);
$_SESSION['attemptAccessPage'] = 'AddAlbum.php';
if (!isset($_SESSION['loginUser'])){
    header("location: Login.php");
    exit();
}
$selectValue = $descriptionValue = $titleError= "";
$loginUser = unserialize($_SESSION['loginUser']);
$userName = $loginUser->getName();
$userId = $loginUser->getId();
if (isset($btnSubmit) && ($hiddenKey == $_SESSION['key'])){
    $validateTitleSuccess = ValidateTitle($title);
    if (!$validateTitleSuccess){
        $selectValue = $accessCode;
        $descriptionValue = $description;
        $titleError = "Title cannot be blank";
    }
    else {
        addNewAlbum($title, $description, $userId, $accessCode);
         echo "<script>ShowDiaglogBox('A new album has been saved successfully')</script>";
           
    }
}
$accessibilityCode = getAccessCodeFromAccessibility();
$_SESSION['key'] = mt_rand(1, 100000);
?>
<div class = "new-album">
    <h1 class="new-album-title">Create New Album</h1>
    <p class="label-padding">Welcome <span class="highlight"> <?php echo "$userName" ?>!</span>(not you? change user <a href="Login.php">here</a>)</p>
    <br/>
    <form action="AddAlbum.php" role="form" method="post">
        <input type="hidden" name="hiddenKey" value ="<?php echo $_SESSION['key']?>" />
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Title:</div>
            <div class = "col-sm-4">
                <input type = "text" class = "form-control" name = "title"/>
            </div>
            <div class="error col-sm-6"><?php echo $titleError ?></div>
        </div>
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Accessibility:</div>
            <div class = "col-sm-4">
               <select class = 'form-control' name = 'accessCode'>
               <?php 
                    foreach ($accessibilityCode as $eachCode){
                        $accessCode = $eachCode->getCode();
                        $description = $eachCode->getDescription();
                        if ($selectValue == $accessCode){
                            echo "<option value = '$accessCode' selected = 'selected'>$description</option>";
                        }
                        else{
                            echo "<option value = '$accessCode'>$description</option>";
                        }
                    }
                ?>
               </select>
            </div>
        </div>
         <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Description:</div>
            <div class = "col-sm-4">
                <textarea  class = "form-control" rows= "7" name = "description" ><?php echo "$descriptionValue" ?></textarea>
            </div>
        </div>  
        <div class="row  v-margin label-padding">
            <div class="col-sm-1 btn-submit "><button type = "submit" name = "btnSubmit" class = "btn btn-success btn-block" >Submit</button></div>
            <div class="col-sm-1 btn-clear "><button type ="submit" class = "btn btn-warning btn-block" name = "btnclear">Clear</button></div>
        </div>
    </form>
</div>

<?php include 'ProjectCommon/Footer.php' ?>
