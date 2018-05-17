<?php
session_start();
include 'ProjectCommon/Header.php';
include 'ProjectCommon/Functions.php';
extract($_POST);
$idError = $nameError = $phoneError = $passwordError = $repasswordError = "";
$idVal = $nameVal = $phoneVal = $passwordVal = $repassowrdVal = "";

// prevent double submission
if (isset($btnSignup) && ($hiddenKey == $_SESSION['key'])){
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
    // validate student name
    $nameValidateSuccess = ValidateUserName($userName);
    if (!$nameValidateSuccess) {
        $nameError = "User name cannot be blank";
        $validateSuccess = FALSE;
    }
    else {
        $nameVal = $userName;
    }
    
    // validate phone number
    $phoneNumberValidateSuccess = ValidatePhone($phoneNumber);
    if (!$phoneNumberValidateSuccess) {
        $phoneError = "Incorrent phone number";
        $validateSuccess = FALSE;
    }
    else {
        $phoneVal = $phoneNumber;
    }
    // validate password
    $passwordValidate = ValidatePassword($password);
    if ($passwordValidate == "blank error"){
        $passwordError = "Password cannot be blank;";
        $validateSuccess = FALSE;
    }
    else if ($passwordValidate == "length error") {
        $passwordError = "Password should be at lease 6 characters long";
        $validateSuccess = FALSE;
    }
    else if ($passwordValidate == "format error") {
        $passwordError = "Password should containt at lease one upper case, one lowercase and one digit";
        $validateSuccess = FALSE;
    }
    else {
        $passwordVal = $password;
         // compare 2 input passwords
        $repeatPasswordVlidateSuccess = ValidatePasswordMatch($password, $password2);
        if (!$repeatPasswordVlidateSuccess) {
            $repasswordError = "Password does not match";
            $validateSuccess = FALSE;
        }
        else {
            $repassowrdVal = $password2;
        }
    } 
    
    if ($validateSuccess) {
        
        $saveRecordSuccess = SaveUserRecord($userId, $userName, $phoneNumber, $password);
        if (!$saveRecordSuccess){
            $idError = "A user with this ID has already signed up";
        }
        else {
            echo "<script>ShowDiaglogBox('A new user has been added successfully')</script>";
            $idVal = $nameVal = $phoneVal = $passwordVal = $repassowrdVal = "";
        }
    }
}
$_SESSION['key'] = mt_rand(0, 1000000);
?>
<div class = "signup">
    <h1 class="signup-title">Sign Up</h1>
    <p style="padding-left: 70px">All fields are required</p>
    <br/>
    <form action = "NewUser.php" role="form" method="post">
        <input type="hidden" name="hiddenKey" value="<?php echo $_SESSION['key'] ?>"/>
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">User ID:</div>
            <div class = "col-sm-2 field-length">
                <input type = "text" class = "form-control" name = "userId" placeholder="Enter user Id" <?php echo "value = '$idVal'" ?>/>
            </div>
            <div class="error col-sm-6"><?php echo $idError ?></div>
        </div>
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Name:</div>
            <div class = "col-sm-2 field-length">
                <input type = "text" class = "form-control" name = "userName" placeholder="Enter user name" <?php echo "value = '$nameVal'" ?>/>
            </div>
            <div class="error col-sm-6"><?php echo $nameError ?></div>
        </div>
        <div class= "row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Phone Number: <br/>(nnn-nnn-nnnn)</div>
            <div class = "col-sm-2 field-length">
                <input type = "text" class = "form-control" name = "phoneNumber" placeholder="nnn-nnn-nnnn" <?php echo "value='$phoneVal'" ?> />
            </div>
            <div class="error col-sm-6"><?php echo "$phoneError"; ?></div>
        </div>
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Password:</div>
            <div class = "col-sm-2 field-length">
                <input type = "password" class = "form-control" name = "password" placeholder="Confirm password" <?php echo "value = '$passwordVal'" ?>/>
            </div>
            <div class="error col-sm-6"><?php echo $passwordError ?></div>
        </div>
         <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-2 label-padding highlight label-length">Password Again:</div>
            <div class = "col-sm-2 field-length">
                <input type = "password" class = "form-control" name = "password2" placeholder="Enter password" <?php echo "value = '$repasswordVal'" ?>/>
            </div>
            <div class="error col-sm-6"><?php echo $repasswordError ?></div>
        </div>
        <div class="row  h-margin v-margin">
            <div class="col-sm-1 btn-submit"><button type = "submit" name = "btnSignup" class = "btn btn-success btn-block" >Submit</button></div>
            <div class="col-sm-1 btn-clear"><button type ="submit" class = "btn btn-warning btn-block" name = "btnclear">Clear</button></div>
        </div>
    </form>
</div>
<?php include 'ProjectCommon/Footer.php'; ?>


