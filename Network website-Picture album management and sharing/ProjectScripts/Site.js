    $(document).ready(function(){
        var imgWidth = $('.img-container img').width();
       $('.img-container').width(imgWidth);
    
      $('.deleteAlbum').on('click', function(e){
        e.preventDefault();
        var deleteLink = $(this).attr('href');
        $.Zebra_Dialog("All pictures in the ablum will be delete with the ablum. Are you sure?", {
               type: 'warning',
               title: 'Warining Information',
               buttons: [
                   {caption: 'Confirm', callback: function(){
                           window.location.replace(deleteLink);
                   } },
                   { caption: 'Cancel' }
               ]
        });
    });
});

function ShowDiaglogBox(content){
    $.Zebra_Dialog(content, {
        type: 'information',
        title: 'Success Information',
        buttons:[{caption: 'Ok'}]
    });
}

function ConfirmDefriend(){
    var confirmLink = "MyFriends.php?defriend=yes";
    $.Zebra_Dialog('The selected friends will be defriended!',{
        type: 'confirmation',
        title: 'Confirmation Message',
        buttons: [{
                caption: 'OK', callback: function(){
                    window.location.replace(confirmLink);
                }
        }, { caption: 'Cancel' }]
    });
}
function ConfirmDeny(){
    var confirmLink = "MyFriends.php?deny=yes";
    $.Zebra_Dialog('The selected friends will be defriended!',{
        type: 'confirmation',
        title: 'Confirmation Message',
        buttons: [{
                caption: 'OK', callback: function(){
                    window.location.replace(confirmLink);
                }
        }, { caption: 'Cancel' }]
    });
}


function run_waitMe(){

$('body').waitMe({
 
effect: 'ios',

//place text under the effect (string).

text: 'Please waiting...',

//background for container (string).

bg: 'rgba(255,255,255,0.7)',

//color for background animation and text (string).

color: '#111'
});
}

function changeAlbum(){
    var albumId = $('#selectAlbum').val();
    var link = "MyPictures.php?albumId=" + albumId;
    window.location.replace(link);
}

function changeShareAlbum(){
    var albumId = $('#selectAlbum').val();
    var link = "FriendPictures.php?albumId=" + albumId;
    window.location.replace(link);
}

