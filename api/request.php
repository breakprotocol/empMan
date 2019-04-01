<?php 
include 'connect.php';

$url =  $_SERVER['REQUEST_URI'];	
$url = explode("/", $url);

if(isset($url[4]))
$service = $url[4];

if(isset($url[5]))
$method = $url[5];


$data = file_get_contents('php://input');

switch($service)
{

	case "banners":
	banners($method,$mysqli,$data);
	break;

	case "categories":
	categories($method,$mysqli,$data);
	break;

	case "sub_categories":
	sub_categories($method,$mysqli,$data);
	break;

	case "products":
	product($method,$mysqli,$data);
	break;

	case "upload":
	uploadImage($method,$mysqli,$data);
	break;

	case "login":
	login($method,$mysqli,$data);
	break;

	case "contacts":
	contacts($method,$mysqli,$data);
	break;

	default :
	echo "Don't do this";
	break;
}

function getCount($mysqli,$table){
	$query = "SELECT COUNT(*) FROM $table";
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
	$row =  $result->fetch_row();
	$total =  $row[0];
	return $total;	
}

function getAll($mysqli,$data,$query,$table){
	// Find out how many items are in the table
	$total =  getCount($mysqli,$table);
	// How many items to list per page
		 $limit = 10;
	// How many pages will there be
		 $pages = ceil($total / $limit);
	// What page are we currently on?
		 $page = $data['page'];
	// Calculate the offset for the query
		 $offset = ($page - 1)  * $limit;

		 if(!$query){
			 $query = "SELECT * from $table order by _id limit ? offset ?";
		 }

		 $stmt=$mysqli->prepare($query);
		 $stmt->bind_param('ss', $limit,$offset);
		 $stmt->execute();
		 $result = $stmt->get_result();
		 $contacts = mysqli_fetch_all ($result, MYSQLI_ASSOC);

		$myObj = new \stdClass();
		$myObj->result = $contacts;
		$myObj->totalCount = $total;
		
		return $myObj;

}

function contacts($method,$mysqli,$data)
{
	$data = json_decode($data, true);
	
	if ($method=="getAll")
	{
		$query=null;
		$sendObj = getAll($mysqli,$data,$query,'contacts');
		echo json_encode($sendObj);
	}

	if ($method=="getOne")
	{

		$stmt = $mysqli->prepare('SELECT * FROM contacts WHERE _id = ?');
		$stmt->bind_param('s', $data['_id']);
		$stmt->execute();
		$result = $stmt->get_result();
		$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		echo json_encode($json );
	}

	if ($method=="create")
	{

		//Getting the data 
		$name = $data['name'];
		$number = $data['number'];
		$email = $data['email'];
		$address = $data['address'];
		$company_name = $data['companyName'];
		$website = $data['website'];
		$note = $data['note'];
		
		
	
		$stmt = $mysqli->prepare('INSERT INTO contacts (name, number, email, address, company_name, website, note) VALUES (?, ?, ?, ?, ?, ?, ?)');
		$stmt->bind_param('sssssss', $name,$number,$email,$address,$company_name,$website,$note);
		$result = $stmt->execute();
		
		if(json_encode($result)){
			$id = mysqli_insert_id($mysqli);
			$myObj = new \stdClass();
			$myObj->id =$id;
			$myObj->status = $result;
			echo json_encode($myObj);
		 }else{
			echo json_encode($result);
		 }
	}

	if ($method=="delete")
	{

		$id = $data['id'];
		$stmt = $mysqli->prepare('DELETE FROM contacts WHERE _id = ?');
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();
		echo json_encode($result);
	}

	if($method=="update")
	{
		$query = "UPDATE contacts SET";
		$comma = " ";
		$id = $data['_id'];
		$whitelist = array(
			'name',
			'number',
			'email',
			'address'
		);

		foreach($data as $key => $val) {
			if( ! empty($val) && in_array($key, $whitelist)) {
				$query .= $comma . $key . " = '" . $mysqli->real_escape_string(trim($val)) . "'";
				$comma = ", ";
			}
		}
		$query .= " where _id = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();

		echo $result;
	}

}

function login($method,$mysqli,$data)
{
	// echo $data;
	$data = json_decode($data,true);
	// print_r $data;
	if($method=="login")
	{
		$username = $data['username'];
		// echo $username;
		$password = $data['password'];
		$loginResponse = array();
		$stmt=$mysqli->prepare('SELECT * FROM user_details where user_name = ?');
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows === 1)
		{
			$user_details = mysqli_fetch_all ($result, MYSQLI_ASSOC);
			json_encode($user_details);
			if (password_verify($password, $user_details[0]['user_pass']))
			{	
				json_encode($user_details[0]['access_level']);
				$loginResponse = array('access' => $user_details[0]['access_level'],'success' => 'true');
				echo json_encode($loginResponse);
		}
			else
			{
				$loginResponse = array('success' => 'false');
				echo json_encode($loginResponse);
			}	
				
		}
		else
		{
			$loginResponse = array('success' => 'false');
			echo json_encode($loginResponse);
		}
		

	}

	if($method=="register")
	{
		$username = $data['username'];
		$password = $data['password'];
		$access = $data['access'];
		$password_hash = password_hash($password,PASSWORD_DEFAULT);
		$stmt = $mysqli->prepare('INSERT INTO user_details(user_name,user_pass,access_level) values (?, ?, ?)');
				$stmt->bind_param('sss',$username,$password_hash,$access);
				$result = $stmt->execute();
				if($result)
					echo $result;
				else
				{
					echo "Theres some problem inserting the new raw material";
					
				}
	}

}

function banners($method,$mysqli,$data){
	$data = json_decode($data, true);
	if ($method=="getAll")
	{
			$query="SELECT * FROM `banners`";
			$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
			$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
			echo json_encode($json);
	}

	if ($method=="create")
	{
		//Getting the data 
		$link = $data['url'];
		$stmt = $mysqli->prepare("INSERT INTO banners (link) VALUES (?)");
		$stmt->bind_param('s', $link);
		$result = $stmt->execute();
		echo json_encode($result);
	}

	if ($method=="delete")
	{
		$id = $data['id'];
		$stmt = $mysqli->prepare('DELETE FROM banners WHERE _id = ?');
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();
		echo json_encode($result);
	}
}


function categories($method,$mysqli,$data)
{
	$data = json_decode($data, true);
	
	if ($method=="getAll")
	{
		if($data && $data['page']){
			$query="SELECT * FROM `categories` ORDER BY `categories`.`priority` ASC limit ? offset ?";
			$sendObj = getAll($mysqli,$data,$query,'categories');
			echo json_encode($sendObj);
		}else{
			$query="SELECT * FROM `categories`";
			$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
			$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
			echo json_encode($json );
		}
	
	}

	if ($method=="getOne")
	{

		$stmt = $mysqli->prepare('SELECT * FROM categories WHERE _id = ?');
		$stmt->bind_param('s', $data['_id']);
		$stmt->execute();
		$result = $stmt->get_result();
		$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		echo json_encode($json );

		// echo "pp";
		// $query="select * from categories where _id=" + $data['_id'];
		// echo $query;
		// $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		// $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		// echo json_encode($json );
	}

	if ($method=="create")
	{

		//Getting the data 
		$name = $data['name'];
		$priority = $data['priority'];
	
		$stmt = $mysqli->prepare('INSERT INTO categories (name, priority) VALUES (?, ?)');
		$stmt->bind_param('ss', $name,$priority);
		$result = $stmt->execute();
		echo json_encode($result);
	}

	if ($method=="delete")
	{

		$id = $data['id'];
		$stmt = $mysqli->prepare('DELETE FROM categories WHERE _id = ?');
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();
		echo json_encode($result);
	}

	if($method=="update")
	{
		$query = "UPDATE categories SET";
		$comma = " ";
		$id = $data['_id'];
		$whitelist = array(
			'name',
			'priority'
		);

		foreach($data as $key => $val) {
			if( ! empty($val) && in_array($key, $whitelist)) {
				$query .= $comma . $key . " = '" . $mysqli->real_escape_string(trim($val)) . "'";
				$comma = ", ";
			}
		}
		$query .= " where _id = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();

		echo $result;
	}

}

function sub_categories($method,$mysqli,$data)
{

	$data = json_decode($data, true);

	if ($method=="getAll"){

		if($data && $data['page']){
			$query = "SELECT sub_categories._id ,sub_categories.name AS sub_cat_name, categories._id AS cat_id,categories.name AS cat_name FROM sub_categories LEFT JOIN categories ON sub_categories.category=categories._id order by _id limit ? offset ?";
			$sendObj = getAll($mysqli,$data,$query,'sub_categories');
			echo json_encode($sendObj);
		}else{
			$query="SELECT sub_categories._id ,sub_categories.name AS sub_cat_name, categories._id AS cat_id,categories.name AS cat_name FROM sub_categories LEFT JOIN categories ON sub_categories.category=categories._id";
			$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
			$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
			echo json_encode($json );
		}
	}else if ($method=="getAllByCat"){
		$category = $data['category'];
		$stmt = $mysqli->prepare('SELECT * FROM sub_categories WHERE category = ?');
		$stmt->bind_param('s', $category);
		$stmt->execute();
		$result = $stmt->get_result();
		$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		echo json_encode($json );
	}else if($method=="create"){
		//Getting the data 
		$name = $data['name'];
		$category = $data['category'];
	
		$stmt = $mysqli->prepare('INSERT INTO sub_categories (name, category) VALUES (?, ?)');
		$stmt->bind_param('ss', $name,$category);
		$result = $stmt->execute();
		echo json_encode($result);
	}else {
		if(isset($data['id'])){
			if ($method=="getOneById"){
					$id = $data['id'];
					$stmt = $mysqli->prepare('SELECT sub_categories._id ,sub_categories.name AS sub_cat_name, categories._id AS cat_id,categories.name AS cat_name FROM sub_categories LEFT JOIN categories ON sub_categories.category=categories._id WHERE sub_categories._id = ?');
					$stmt->bind_param('s', $id);
					$stmt->execute();
					$result = $stmt->get_result();
					$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
					echo json_encode($json );
			}else if ($method=="delete"){
				$id = $data['id'];
				$stmt = $mysqli->prepare('DELETE FROM sub_categories WHERE _id = ?');
				$stmt->bind_param('s', $id);
				$result = $stmt->execute();
				echo json_encode($result);
			}else if($method=="update"){
				$query = "UPDATE sub_categories SET";
				$comma = " ";
				$id = $data['id'];
				$whitelist = array(
					'name',
					'category'
				);
		
				foreach($data as $key => $val) {
					if( ! empty($val) && in_array($key, $whitelist)) {
						$query .= $comma . $key . " = '" . $mysqli->real_escape_string(trim($val)) . "'";
						$comma = ", ";
					}
				}
				$query .= " where _id = ?";
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param('s', $id);
				$result = $stmt->execute();
		
				echo $result;
			}
		}else{
			echo "Parameters are not defined properly\n";
		}
	}
}

function product($method,$mysqli,$data)
{

		$data = json_decode($data, true);

		// create,getAll
		if ($method=="getAllBySubCat"){
			$stmt = $mysqli->prepare('SELECT * FROM products WHERE sub_categories = ?');
			$stmt->bind_param('s', $sub_cat);
			$stmt->execute();
			$result = $stmt->get_result();
			$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
			echo json_encode($json );
		}else if ($method=="getAll"){
			if($data && $data['page']){
				$query = "SELECT products._id ,products.name AS prod_name,products.link AS prod_img,products.priority AS prod_priority,products.status AS prod_status,sub_categories.name AS sub_cat_name FROM products LEFT JOIN sub_categories ON products.sub_categories=sub_categories._id order by _id limit ? offset ?";
				$sendObj = getAll($mysqli,$data,$query,'products');
				echo json_encode($sendObj);
			}else{
				$stmt = $mysqli->prepare('SELECT products._id ,products.name AS prod_name,products.link AS prod_img,products.priority AS prod_priority,products.status AS prod_status,sub_categories.name AS sub_cat_name FROM products LEFT JOIN sub_categories ON products.sub_categories=sub_categories._id');
				$stmt->execute();
				$result = $stmt->get_result();
				$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
				echo json_encode($json );
			}
		}else if($method=="create"){
			//Getting the data 
			$name = $data['name'];
			$sub_categories = $data['sub_categories'];
			$link = $data['link'];
			$status = 1;
			$priority = intval($data['priority']);
		
			$stmt = $mysqli->prepare('INSERT INTO products (name, sub_categories, link, status, priority) VALUES (?, ?, ?, ?, ?)');
			$stmt->bind_param('sssss', $name,$sub_categories,$link,$status,$priority);
			$result = $stmt->execute();
			echo json_encode($result);
		}else {
			if(isset($data['id'])){
				if ($method=="getOneById"){
						$id = $data['id'];
						$stmt = $mysqli->prepare('SELECT products._id ,products.name AS prod_name,products.link AS prod_link,products.status AS prod_status,products.priority AS prod_priority, sub_categories._id AS sub_cat_id,sub_categories.name AS sub_cat_name,categories._id AS cat_id,categories.name AS cat_name FROM products LEFT JOIN sub_categories ON products.sub_categories=sub_categories._id LEFT JOIN categories ON sub_categories.category=categories._id  WHERE products._id = ?');
						$stmt->bind_param('s', $id);
						$stmt->execute();
						$result = $stmt->get_result();
						$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
						echo json_encode($json );
				}else if ($method=="delete"){
					$id = $data['id'];
					$stmt = $mysqli->prepare('DELETE FROM products WHERE _id = ?');
					$stmt->bind_param('s', $id);
					$result = $stmt->execute();
					echo json_encode($result);
				}else if($method=="update"){
					$query = "UPDATE products SET";
					$comma = " ";
					$id = $data['id'];
					$whitelist = array(
						'name',
						'sub_categories',
						'status',
						'link',
						'priority'
					);
			
					foreach($data as $key => $val) {
						if(isset($val) && in_array($key, $whitelist)) {
							$query .= $comma . $key . " = '" . $mysqli->real_escape_string(trim($val)) . "'";
							$comma = ", ";
						}
					}
					$query .= " where _id = ?";
					$stmt = $mysqli->prepare($query);
					$stmt->bind_param('s', $id);
					$result = $stmt->execute();
			
					echo $result;
				}
			}else{
				echo "Parameters are not defined properly\n";
			}
		}
}

function compress(){
	// Create new imagick object
	$im = new Imagick("File_Path/Image_Name.jpg");

	// Optimize the image layers
	$im->optimizeImageLayers();

	// Compression and quality
	$im->setImageCompression(Imagick::COMPRESSION_JPEG);
	$im->setImageCompressionQuality(25);

	// Write the image back
	$im->writeImages("File_Path/Image_Opti.jpg", true);
}



function compress_image($source_url, $destination_url, $quality) {

	$info = getimagesize($source_url);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif')
    	$image = imagecreatefromgif($source_url);
	elseif ($info['mime'] == 'image/png')
	ini_set('memory_limit', '-1');
		$image = imagecreatefrompng($source_url);
        imagejpeg($image, $destination_url, $quality);
    return $destination_url;
}



function uploadImage($method,$mysqli,$data)
{
	if($method=="upload"){
		define ('SITE_ROOT', realpath(dirname(__DIR__)));
		$target_dir =SITE_ROOT . ("/uploads/") ;
	}else if($method=="banner"){
		define ('SITE_ROOT', realpath(dirname(__DIR__)));
		$target_dir =SITE_ROOT . ("/uploads/banners/") ;
	}
	$name =$_FILES["file"]["name"];
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
	move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
	// compress_image($target_file,$target_file,'25');
	echo $_FILES["file"]["name"]; 
}

?>