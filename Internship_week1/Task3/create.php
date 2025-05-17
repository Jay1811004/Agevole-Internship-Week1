<?php
// Include database connection
require_once "config/config.php";

$name = $price = $description = "";
$name_err = $price_err = $description_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a product name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter the price.";     
    } elseif (!is_numeric(trim($_POST["price"])) || floatval(trim($_POST["price"])) <= 0) {
        $price_err = "Please enter a valid price.";
    } else {
        $price = trim($_POST["price"]);
    }

    $description = trim($_POST["description"]);

    if (empty($name_err) && empty($price_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO products (name, price, description) VALUES (?, ?, ?)";
         
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sds", $param_name, $param_price, $param_description);

            $param_name = $name;
            $param_price = $price;
            $param_description = $description;
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php?success=created");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2>Add New Product</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control <?= (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?= $name; ?>">
                                <span class="invalid-feedback"><?= $name_err; ?></span>
                            </div>    
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control <?= (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?= $price; ?>">
                                <span class="invalid-feedback"><?= $price_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"><?= $description; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <input type="submit" class="btn btn-primary" value="Add Product">
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</body>
</html>
