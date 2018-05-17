<?php
$upOne = realpath(_DIR_. '/..');
define(ORIGINAL_PICTURES_DIR, $upOne."Pictures/OriginalPictures");
define(ALBUM_PICTURES_DIR, $upOne."Pictures/AlbumPictures");
define(ALBUM_THUMBNAILS_DIR, $upOne."Pictures/AlbumThumbnails");

define(IMAGE_MAX_WIDTH, 1024);
define(IMAGE_MAX_HEIGHT, 800);


define(THUMB_MAX_WIDTH, 100);
define(THUMB_MAX_HEIGHT, 100);

define(MYSELF, 'myself');
define(REPEATREQUEST, 'repeat request');
define(DENYREQUEST, 'request has been rejected');
define(NOUSER, 'this user does not exist');
define(BEFRIENDALREADY, 'you are friends already');
define(SUCCESS, 'success');
define(SEND, 'send');
$supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
date_default_timezone_set("America/Tornoto");
