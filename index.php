<?php

$BLOG_URL = "http://www.example.com/";
$WP_URL = "http://www.example.com/wp/";
$WP_USER = "admin";
$WP_PASS = "password1";


//----------------------------------------
//	WordPress外部投稿
//
//	**引数**
//	$domain(string)	投稿したいWordPressを置いているサーバのドメイン
//	$user(string)		WordPressのユーザー
//	$pass(string		WordPressのパスワード
//	$author(int)		投稿者のID
//	$title(string)		投稿タイトル
//	$contents(string)	投稿本文
//	$status(string)	投稿状態。デフォルトは下書き。publishだと公開
//	$category(int)	投稿カテゴリ。デフォルトは未分類
//	$tag(array)		投稿タグ。デフォは何もなし
//	$img(string)		アイキャッチ画像。[./img/test.jpg]のようにディレクトリ形式で指定。デフォは何もなし
//
//	**返り値**
//	bool(true or false)
//----------------------------------------

function postWordPress( $domain, $user, $pass, $author = 1, $title, $contents, $status = 'draft', $category = 1, $tag = array(), $img = '' )
{
	//必須引数のチェック
	if( !$domain || !$user || !$pass || !$title || !$contents ){
		return false;
	}

	//ライブラリの読み込み
	include_once('./IXR_Library.php');
	$client = new IXR_Client($domain . 'xmlrpc.php');

	//画像が指定されている場合はチェックとアップロード
	if( $img && file_exists($img) ){
		$imgInfo = getimagesize($img);
		$type = $imgInfo['mime'];
		$bits = new IXR_Base64(file_get_contents($img));

		$imgData = $client->query(
			'wp.uploadFile',
			1,
			$user,
			$pass,
			array(
				'name' => 'test.jpg',
				'type' => $type,
				'bits' => $bits,
				'overwrite' => true,
                'post_id' => $post_id
			)
		);
		$imgResult = $client->getResponse();
        // echo $imgResult;
	}

	$postData = array(
		'post_author' => $author,
		'post_status' => $status,
		'post_title' => $title,
		'post_content' => $contents,
		'terms' => array('category' => array($category)),
		'terms_names' => array('post_tag' => $tag)
	);

	if( $imgResult ){
		$postData['post_thumbnail'] = $imgResult['id'];
	}

    $status = $client->query('wp.newPost', 1, $user, $pass, $postData);
    if(!$status){
        die('Something went wrong - '.$client->getErrorCode().' : '.$client->getErrorMessage());
    } else {
        $post_id = $client->getResponse(); //返り値は投稿ID
    	return $post_id;
    }
}
// var_dump($_FILES['photo']);
if($_FILES['photo'][tmp_name]){
	$result = postWordPress(
		$WP_URL,
		$WP_USER,
		$WP_PASS,
		1,
		date(DATE_RFC2822),
		date(DATE_RFC2822),
		'publish',
		1,
		array(),
		$_FILES['photo'][tmp_name]
	);
	$posturl = $BLOG_URL.$result."/";
}

if($result && $_POST["result_type"]=="result_code"){
	echo "QR Code";
}else if($result && $_POST["result_type"]=="result_id"){
	echo $result;
}else if($result && $_POST["result_type"]=="result_url"){
	echo $posturl;
}else if($result && $_POST["result_type"]=="result_json"){
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($posturl);
}else{
	 header("Content-Type: text/html; charset=UTF-8"); ?>
 <!DOCTYPE html>
 <html>
 <head>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <title>Photo Upload Form.</title>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 </head>
 <body>
 	<?php if($result):
 		$url = $posturl;
 	?>
 	<p>Upload complete to <a href="<?php echo $url ?>" target="_blank"><?php echo $url; ?></a></p>
 	<?php endif ?>
     <form enctype="multipart/form-data" action="." method="POST">
         <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
         <h3>Photo File.</h3>
         <input type="file" name="photo" />

         <h3>Result Type.</h3>
         <label><input type="radio" name="result_type" value="result_page"/>ページ</label>
         <label><input type="radio" name="result_type" value="result_url" checked="checked" />URLのみ</label>
         <label><input type="radio" name="result_type" value="result_id" />IDのみ</label>
         <label><input type="radio" name="result_type" value="result_json" />JSON</label>
         <label><input type="radio" name="result_type" value="result_code" />QRコード</label>

         <h3>Submit</h3>
         <input type="submit" value="Submit" />

     </form>
 </body>
 </html>
<?php } ?>
