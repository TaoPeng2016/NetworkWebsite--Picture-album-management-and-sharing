<?php
session_start();
include 'ProjectCommon/Header.php';
include_once 'ProjectCommon/Functions.php';

$_SESSION['attemptAccessPage'] = 'MyFriends.php';
if (!isset($_SESSION['loginUser'])){
    
    header("location: Login.php");
    exit();
}
$_SESSION['selectedSharedAlbumId'] = NULL;
$_SESSION['selectedSharedPictureId'] = NULL;
$_SESSION['friendId'] = NULL;
$loginUser = unserialize($_SESSION['loginUser']);
$userName = $loginUser->getName();
$userId = $loginUser->getId();
if (urldecode($_GET['deny']) == 'yes'){
    if (isset($_SESSION['denyRequests'])){
        $denyRequests = unserialize($_SESSION['denyRequests']);
        if (sizeof($denyRequests) > 0){
            for ($i = 0; $i < sizeof($denyRequests); $i++){
                DenyFriendRequest($denyRequests[$i], $userId);
            }
           
            header("location: MyFriends.php");
        }
    }
}
else {
    $_SESSION['denyRequests'] = NULL;
}
if (urldecode($_GET['defriend']) == 'yes'){
    if (isset($_SESSION['defriends'])){
        $defriends = unserialize($_SESSION['defriends']);
        if (sizeof($defriends) > 0){
            for ($i = 0; $i < sizeof($defriends); $i++){
                DefriendFriendship($userId, $defriends[$i]);
            }
        }
       
        header("location: MyFriends.php");
    }
}
else {
    $_SESSION['defriends'] = NULL;
}
extract($_POST);
if (isset($btnAccept) && ($_SESSION['key'] == $hiddenKey2)){
    if (sizeof($requests) > 0){
        for($i = 0; $i < sizeof($requests); $i++){
            AcceptFriendRequest($requests[$i], $userId);
        }
        header("location: MyFriends.php");
    }
}
if (isset($btnDefriend) && ($_SESSION['key'] == $hiddenKey)){
    if (sizeof($defriends) > 0){
       $_SESSION["defriends"] = serialize($defriends);
       echo "<script>ConfirmDefriend()</script>";
    }
}
if (isset($btnDeny) && ($_SESSION['key'] == $hiddenKey2) ){
    if (sizeof($requests) > 0){
        $_SESSION["denyRequests"] = serialize($requests);
        echo "<script>ConfirmDeny()</script>";
    }
}
$friends = array();
$friendRequesters = array();
// get friendships
$friendships = GetMyFriends($userId);
$frinedshipRequests = GetFriendRequest($userId);
// get friends'information
if ($friendships != NULL){
    foreach ($friendships as $friendship) {
        $friendId = $friendship->getRequesterId() != $userId? $friendship->getRequesterId(): $friendship->getRequesteeId();
        $friend = GetUserById($friendId);
        if ($friend != NULL){
            array_push($friends, $friend);
        }
    }
}
if ($frinedshipRequests != NULL ){
    foreach ($frinedshipRequests as $friendshipRequest){
        $requesterId = $friendshipRequest->getRequesterId();
        $requester = GetUserById($requesterId);
        if ($requester != NULL){
            array_push($friendRequesters, $requester);
        }
    }
}
$_SESSION['key'] = mt_rand(0, 1000000);
?>
<div class="my-friends">
    <h2 class="center v-margin">My Friends</h2>
    <p>Welcome <span class="highlight"><?php echo "$userName" ?>!</span>(not you? change user <a href="Login.php">here</a>)</p>
    <div>
       
        <div class = "link-NewFriend"><a href="AddFriend.php">Add Friends</a></div>
    </div>
   
    <form action="MyFriends.php" role="form" method="post">
        <label>Friends:</label>
        <input type="hidden" name="hiddenKey" value="<?php echo $_SESSION['key'] ?>"/>
        <div class="friend-table">
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Share Albums</th>
                    <th>Defriend</th>
                </thead>
                <?php
                    if (sizeof($friends)!= 0){
                        foreach ($friends as $friend){
                            echo "<tr>";
                            $friendName = $friend->getName();
                            $friendId = $friend->getId();
                            $albumLink = "FriendPictures.php?friendId=".$friendId;
                            $friendShareAlbums = GetUserSharedAlbum($friendId);
                            $sharedAlbumsQty = sizeof($friendShareAlbums);
                            echo "<td><a href='$albumLink'>$friendName</a></td><td>$sharedAlbumsQty</td>";
                            echo "<td><input type = 'checkbox' name = 'defriends[]'/ value = '$friendId'></td>";
                            echo "</tr>";
                        }
                    }
                ?>
                
            </table>
        </div>
        <div class = "col-sm-2 album-save v-margin"><button type = "submit" name = "btnDefriend" id="btnDefriend" class = "btn btn-warning btn-block">Defriend Selected</button></div>
    </form>
   
    <div class="friend-table-request">
        
         <form action="MyFriends.php" role="form" method="post">
             <label>Friend Requests</label>
             <input type="hidden" name="hiddenKey2" value="<?php echo $_SESSION['key'] ?>"/>
             <div class="request-list">
             <table class="table">
                  <thead>
                    <th>Name</th>
                    <th>Accept or Deny</th>
                  </thead>
                  <?php
                    if (sizeof($friendRequesters) != 0){
                        foreach($friendRequesters as $requester){
                            $requesterName = $requester->getName();
                            $requesterId = $requester->getId();
                            echo "<tr>";
                            echo "<td>$requesterName</td>";
                            echo "<td><input type='checkbox' name='requests[]', value='$requesterId' /></td>";
                        }
                    }
                    echo "</tr>";
                  ?>
              
             </table>
             </div>
               <div class = "col-sm-2 btn-request "><button type = "submit" name = "btnAccept"  class = "btn btn-success btn-block">Accept Selected</button></div>
               <div class = "col-sm-2"><button type = "submit" name = "btnDeny" id="btnDeny" class = "btn btn-warning btn-block">Deny Selected</button></div>
        </form>
    </div>
   </div>  

<?php include 'ProjectCommon/Footer.php' ?>