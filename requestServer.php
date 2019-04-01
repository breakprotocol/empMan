<?php 
include 'connect.php';

$url =  $_SERVER['PHP_SELF'];	
$url = explode("/", $url);

if(isset($url[3]))
$service = $url[3];

if(isset($url[4]))
$method = $url[4];


$data = file_get_contents('php://input');

switch($service)
{
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

	default :
	echo "Don't do this";
	break;
}


function categories($method,$mysqli,$data)
{
	$data = json_decode($data, true);
	
	if ($method=="getAll")
	{
		$query="select * from categories";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		echo json_encode($json );
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
		$query="SELECT sub_categories._id ,sub_categories.name AS sub_cat_name, categories._id AS cat_id,categories.name AS cat_name FROM sub_categories LEFT JOIN categories ON sub_categories.category=categories._id";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		echo json_encode($json );
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
			$stmt = $mysqli->prepare('SELECT products._id ,products.name AS prod_name,products.link AS prod_img,products.priority AS prod_priority,products.status AS prod_status,sub_categories.name AS sub_cat_name FROM products LEFT JOIN sub_categories ON products.sub_categories=sub_categories._id');
			$stmt->execute();
			$result = $stmt->get_result();
			$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
			echo json_encode($json );
		}else if($method=="create"){
			//Getting the data 
			$name = $data['name'];
			$sub_categories = $data['sub_categories'];
			$link = $data['link'];
			$status = intval($data['status']);
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

function uploadImage($method,$mysqli,$data)
{
	define ('SITE_ROOT', realpath(dirname(__DIR__)));


	$target_dir =SITE_ROOT . ("/uploads/") ;
	$name =$_FILES["file"]["name"];
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
	move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
	echo $_FILES["file"]["name"]; 
	

	// $stmt = $mysqli->prepare('select name,MAX(_ID) from images');
	
	// $result = $stmt->execute();
	
	// echo 


	// echo $target_file;
	// $sql = "INSERT INTO MyData (name,filename) VALUES ('".$name."','".basename($_FILES["file"]["name"])."')";

	// if ($conn->query($sql) === TRUE) {
		
	// } else {
	//    echo "Error: " . $sql . "<br>" . $conn->error;
	// }

}

?>