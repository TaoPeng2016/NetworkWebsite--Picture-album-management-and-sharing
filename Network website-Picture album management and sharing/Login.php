<?php
session_start();
include 'ProjectCommon/Header.php';
include 'ProjectCommon/Functions.php';

extract($_POST);
$loginError = $idError = $passwordError = "";
$idVal = $passwordVal = "";
if (isset($btnLogin)) {
    $validateSuccess = TRUE;
    // validate student id
    $idValidateSuccess = ValidateUserId($userId);
    if (!$idValidateSuccess){
        $idError = "User ID cannot be blank";
        $validateSuccess = FALSE;
    }
    else {
        $idVal = $userId;
    }
    
    //validate password
    $passwordValidate = ValidatePassword($password);
    if ($passwordValidate == "blank error"){
        $passwordError = "Password cannot be blank;";
        $validateSuccess = FALSE;
    }
    else{
        $passwordVal = $password;
    }
    if ($validateSuccess) {
        $loginUser = UserLogin($userId, $password);
        
        if ($loginUser != NULL){
            $_SESSION['selectedPictureId'] = NULL;
            $_SESSION['selectedAlbumId'] = Null;
            $_SESSION["loginUser"] = serialize($loginUser);
             if (isset($_SESSION['attemptAccessPage'])) {
                $accesslink = "location:".$_SESSION['attemptAccessPage'];
                header($accesslink);
                
            }
            else{
                header("location:MyAlbums.php");
                exit();
            }
            
            
        }
        else{
            $loginError = "Incorrect user ID and/or Password!";
        }
    }
}
?>
<div class="login-page">
    <h1 class="login-title">Log In</h1>
    <p style="padding-left: 70px">You need to <a href="NewUser.php">sign up</a> if you are a new user</p>
    <span class="loginError"><?php echo $loginError ?></span>
    <form action="Login.php" role="form" method="post">
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">User ID:</div>
            <div class = "col-sm-2 field-length">
                <input type = "text" class = "form-control" name = "userId" placeholder="Enter userId" <?php echo "value = '$idVal'" ?> style="background-color:lightyellow"/>
            </div>
            <div class="error col-sm-6"><?php echo $idError ?></div>
        </div>
        
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Password:</div>
            <div class = "col-sm-2 field-length">
                <input type = "password" class = "form-control" name = "password" placeholder="Confirm password" <?php echo "value = '$passwordVal'"  ?> style="background-color:lightyellow"/>
            </div>
            <div class="error col-sm-6"><?php echo $passwordError ?></div>
        </div>
        <br/>
        <div class="row  h-margin v-margin">
            <div class="col-sm-1 btn-submit"><button type = "submit" name = "btnLogin" class = "btn btn-success btn-block" >Submit</button></div>
            <div class="col-sm-1 btn-clear"><button type ="submit" class = "btn btn-warning btn-block" name = "btnclear">Clear</button></div>
        </div>
    </form>
</div>
<?php include 'ProjectCommon/Footer.php' ?>
