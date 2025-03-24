<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php'; 

function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_OK:
            return "No error, the file was successfully uploaded.";
        case UPLOAD_ERR_INI_SIZE:
            return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
        case UPLOAD_ERR_FORM_SIZE:
            return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
        case UPLOAD_ERR_PARTIAL:
            return "The uploaded file was only partially uploaded.";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded.";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing a temporary folder.";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk.";
        case UPLOAD_ERR_EXTENSION:
            return "A PHP extension stopped the file upload.";
        default:
            return "Unknown error occurred.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $size = $_POST['size']; 
    $color = $_POST['color'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];

        $imageFolderPath = 'Images/'; 
        $imageName = time() . "_" . $imageName;

        $imagePath = $imageFolderPath . $imageName;

        if (move_uploaded_file($imageTmpName, $imagePath)) {
            $imageUrl = $imageName; 

            $created_at = date('Y-m-d H:i:s');

            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, size, color, image, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiissss", $name, $description, $price, $stock, $category_id, $size, $color, $imageUrl, $created_at);

            if ($stmt->execute()) {
                echo "<div class='success'>Product added successfully!</div>";
            } else {
                echo "<div class='error'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='error'>Failed to upload image.</div>";
        }
    } else {
        echo "<div class='error'>No image uploaded or there was an error with the image upload.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    if (!is_numeric($product_id)) {
        echo "<div class='error'>Invalid product ID.</div>";
    } else {
        $delete_sql = "DELETE FROM products WHERE product_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $product_id);

        if ($delete_stmt->execute() && $delete_stmt->affected_rows > 0) {
            echo "<div class='success'>Product deleted successfully!</div>";
        } else {
            echo "<div class='error'>Error: No product found with ID '$product_id' or deletion failed.</div>";
        }

        $delete_stmt->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>About Us</title>
</head>
<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>
    
    <?php include 'moonoverlay.php'; ?>
   
    <style>
      .button-container {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

button.toggle-btn {
    background-color: #000;
    color: #fff; 
    border: 1px solid #000;
    padding: 12px 28px;
    font-size: 16px;
    margin: 0 10px;
    cursor: pointer;
    border-radius: 8px; 
    transition: background-color 0.3s ease, color 0.3s ease;
    font-weight: 500;
}

button.toggle-btn:hover {
    background-color: #fff; 
    color: #000;
}

.form-container {
    display: block;
    max-width: 500px;
    margin: 30px auto;
    padding: 25px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

.form-container h2 {
    text-align: center;
    color: #000;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: 600;
}

.form-container label {
    font-size: 14px;
    color: #000;
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
}

.form-container input[type="text"],
.form-container input[type="number"],
.form-container input[type="file"],
.form-container textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 14px;
    transition: border 0.2s ease;
}

.form-container input[type="text"]:focus,
.form-container input[type="number"]:focus,
.form-container textarea:focus {
    border: 1px solid #000; 
    outline: none;
}

.form-container textarea {
    resize: vertical;
    height: 140px;
}

.form-container input[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #000;
    color: #fff;
    border: 1px solid #000;
    font-size: 16px;
    cursor: pointer;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-weight: 500;
}

.form-container input[type="submit"]:hover {
    background-color: #fff;
    color: #000;
}

.success, .error {
    font-size: 14px;
    text-align: center;
    margin-top: 15px;
}

.success {
    color: #4CAF50; 
}

.error {
    color: #FF3B3B;
}

 .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .product {
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .product img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .product h3, .product p {
            color: #333;
        }

        .product .product-price {
            font-size: 18px;
            font-weight: bold;
        }

        .delete-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<h1>Product Management</h1>

<div class="button-container">
    <button class="toggle-btn" id="add-btn" onclick="toggleForms('add')">Add Product</button>
    <button class="toggle-btn" id="delete-btn" onclick="toggleForms('delete')">Delete Product</button>
</div>

<!-- Add Product Form -->
<div class="form-container" id="add-form">
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br><br>

        <label for="price">Price (£):</label>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required><br><br>

        <label for="category_id">Category ID:</label>
        <input type="number" id="category_id" name="category_id" required><br><br>

        <label for="size">Size:</label>
        <input type="text" id="size" name="size" required><br><br>

        <label for="color">Color:</label>
        <input type="text" id="color" name="color"><br><br>

        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image"><br><br>

        <input type="submit" name="add_product" value="Add Product">
    </form>
</div>

<!-- Delete Product Form -->
<div class="form-container" id="delete-form" style="display: none;">
    <form method="POST">
        <label for="product_id">Product ID to delete:</label>
        <input type="number" id="product_id" name="product_id" required><br><br>

        <input type="submit" name="delete_product" value="Delete Product">
    </form>
</div>

<script>
    function toggleForms(formType) {
        if (formType === 'add') {
            document.getElementById('add-form').style.display = 'block';
            document.getElementById('delete-form').style.display = 'none';
        } else if (formType === 'delete') {
            document.getElementById('add-form').style.display = 'none';
            document.getElementById('delete-form').style.display = 'block';
        }
    }
</script>

</body>
</html>
