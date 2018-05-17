<?php
session_start();
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';

$_SESSION['attemptAccessPage'] = 'MyFriends.php';
if (!isset($_SESSION['loginUser'])){
    
    header("location: Login.php");
    exit();
}
$result = $error= "";

$loginUser = unserialize($_SESSION['loginUser']);
$userName = $loginUser->getName();
$userId = $loginUser->getId();
extract($_POST);
if (isset($btnSubmit) && ($_SESSION['key'] == $hiddenKey)) {
    $validateIdSuccess = ValidateUserId($inviteeId);
    if (!$validateIdSuccess){
        $error = "user Id can not be blank";
    }
    else{
        $success = FALSE;
        $send = FALSE;
        $addFriendshipResult =AddFriendShip($userId, $inviteeId);
       
        switch ($addFriendshipResult) {
            case MYSELF:
                $error = "you cannot send a friend request to yourself";
                break;
            case REPEATREQUEST:
                $error = "repeat request";
                break;
            case NOUSER:
                $error = "the user ID does not exist";
                break;
            case BEFRIENDALREADY:
                $error = "you cannot send a friend request to to someone who is already your friend";
                break;
            case SUCCESS:
                $success = TRUE;
                break;
            case SEND:
                $send = TRUE;
                break;
            default:
                break;
        }
        if ($success || $send) {
            $invitee = GetUserById($inviteeId);
            $inviteeName = $invitee->getName();
            if ($success) {
                $result = "Congratulation! Your request has been accepted by ".$inviteeName."(ID: ".$inviteeId."). Now, you and ".$inviteeName." are friends and be able to view each other's shared albums.";
            }
            else {
                $result ="Your request has sent to ".$inviteeName."(ID: ".$inviteeId."). Once ".$inviteeName." accepts your request, you and ".$inviteeName." will be friends and be able to view each other's shared albums";
            }
        }
    }
}
$_SESSION['key'] = mt_rand(1, 100000);
?>
<div class="add-friend">
    <h1 class="new-album-title">Add Friend</h1>
    <p class="label-padding">Welcome <span class="highlight"> <?php echo "$userName" ?>!</span>(not you? change user <a href="Login.php">here</a>)</p>
    <p class="label-padding">Enter the ID of the user you want to be friend with</p>
    <br/>
    <div class="add-friend-result"><p class="label-padding highlight"><?php echo $result ?></p></div>
    <span class="error label-padding"><?php echo $error ?></span>
    <form action="AddFriend.php" role="form" method="post">
        <input type="hidden" name="hiddenKey" value ="<?php echo $_SESSION['key']?>" />
        <div class="row horizontal-margin vertical-margin">
            <div class ="col-sm-1 label-padding highlight ">ID:</div>
            <div class = "col-sm-4">
                <input type = "text" class = "form-control" name = "inviteeId"/>
            </div>
            <div class="col-sm-2"><button type = "submit" name = "btnSubmit" class = "btn btn-success" >Send Friend Request</div>
        </div>
    </form>
</div>
<?php include 'ProjectCommon/Footer.php' ?>


