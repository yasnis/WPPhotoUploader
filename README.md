# WPPhotoUploader

## Dependancies
+ Put [IXR_Library.php](http://scripts.incutio.com/xmlrpc/ "") to same directory with index.php.

## How to use
+ Set params for your environment. (```$BLOG_URL```, ```$WP_URL```, ```$WP_USER```, ```$WP_PASS```)
+ Upload "index.php" and "IXR_Library.php" to somewhere internet.
+ Upload image and some params to index.php by POST Method from your application.

##Upload Parameters
+ photo:Image to upload.
+ result_type:
	+ result_id:id of wp post.
	+ result_url:url of wp post.
	+ result_json:json of post url.
	+ result_code: qrcode of post url(unavailable).

## TODO
+ Add function to generate QR Code.