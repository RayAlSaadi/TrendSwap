<?php
// checkout_test.php
session_start();
include 'db_connect.php';

// Test class for checkout functionality
class CheckoutTest {
    private $conn;
    private $testUserId = 13; // Use a test user ID
    private $testResults = [];
    
    public function __construct($conn) {
        $this->conn = $conn;
        // Set up test session
        $_SESSION["user_id"] = $this->testUserId;
    }
    
    // Run all tests
    public function runAllTests() {
        $this->testUserFetch();
        $this->testCartFetch();
        $this->testOrderCreation();
        $this->testInventoryUpdate();
        $this->testCartClearing();
        
        $this->displayResults();
    }
    
    // Test user information retrieval
    private function testUserFetch() {
        $user_query = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $user_query->bind_param("i", $this->testUserId);
        $user_query->execute();
        $user_result = $user_query->get_result();
        $user = $user_result->fetch_assoc();
        
        $this->testResults['user_fetch'] = [
            'name' => 'User Information Fetch Test',
            'passed' => ($user && isset($user['user_id'])),
            'data' => $user ? "Found user: {$user['first_name']} {$user['last_name']}" : "No user found"
        ];
    }
    
    // Test cart information retrieval
    private function testCartFetch() {
        $cart_query = $this->conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
        $cart_query->bind_param("i", $this->testUserId);
        $cart_query->execute();
        $cart_result = $cart_query->get_result();
        $cart = $cart_result->fetch_assoc();
        
        $hasItems = false;
        $itemsCount = 0;
        $totalAmount = 0;
        
        if ($cart) {
            $cart_id = $cart["cart_id"];
            
            $item_query = $this->conn->prepare("
                SELECT 
                    ci.quantity, 
                    p.name, 
                    p.price
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.cart_id = ?
            ");
            $item_query->bind_param("i", $cart_id);
            $item_query->execute();
            $result = $item_query->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $itemsCount++;
                $totalAmount += $row['quantity'] * $row['price'];
            }
            
            $hasItems = ($itemsCount > 0);
        }
        
        $this->testResults['cart_fetch'] = [
            'name' => 'Cart Information Fetch Test',
            'passed' => isset($cart['cart_id']),
            'data' => $cart ? "Found cart ID: {$cart['cart_id']}, Items: $itemsCount, Total: £$totalAmount" : "No cart found"
        ];
    }
    
    // Test order creation (simulated)
    private function testOrderCreation() {
        // Begin transaction so we can rollback after testing
        $this->conn->begin_transaction();
        
        try {
            // Get cart information for test
            $cart_query = $this->conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
            $cart_query->bind_param("i", $this->testUserId);
            $cart_query->execute();
            $cart_result = $cart_query->get_result();
            $cart = $cart_result->fetch_assoc();
            
            if (!$cart) {
                throw new Exception("No cart found for test user");
            }
            
            $cart_id = $cart["cart_id"];
            
            // Calculate total amount
            $total_amount = 0;
            $cart_items = [];
            
            $item_query = $this->conn->prepare("
                SELECT 
                    ci.cart_item_id, 
                    ci.quantity, 
                    ci.size,
                    p.product_id, 
                    p.name, 
                    p.price
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.cart_id = ?
            ");
            $item_query->bind_param("i", $cart_id);
            $item_query->execute();
            $result = $item_query->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $row['subtotal'] = $row['quantity'] * $row['price'];
                $total_amount += $row['subtotal'];
                $cart_items[] = $row;
            }
            
            // Create test order
            $create_order = $this->conn->prepare("
                INSERT INTO orders (
                    user_id, 
                    order_date,
                    total_amount,
                    status
                ) VALUES (?, NOW(), ?, 'Test')
            ");
            
            $create_order->bind_param(
                "id", 
                $this->testUserId, 
                $total_amount
            );
            
            $create_order->execute();
            $order_id = $this->conn->insert_id;
            
            $this->testResults['order_creation'] = [
                'name' => 'Order Creation Test',
                'passed' => ($order_id > 0),
                'data' => "Created test order #$order_id with total: £$total_amount"
            ];
            
        } catch (Exception $e) {
            $this->testResults['order_creation'] = [
                'name' => 'Order Creation Test',
                'passed' => false,
                'data' => "Error: " . $e->getMessage()
            ];
        }
        
        // Rollback all changes
        $this->conn->rollback();
    }
    
    // Test inventory update (simulated)
    private function testInventoryUpdate() {
        // Begin transaction so we can rollback after testing
        $this->conn->begin_transaction();
        
        try {
            // Get a test product
            $product_query = $this->conn->prepare("
                SELECT product_id, stock FROM products LIMIT 1
            ");
            $product_query->execute();
            $product_result = $product_query->get_result();
            $product = $product_result->fetch_assoc();
            
            if (!$product) {
                throw new Exception("No products found for testing");
            }
            
            $product_id = $product['product_id'];
            $initial_stock = $product['stock'];
            $test_quantity = 1;
            
            // Test inventory update
            $update_product = $this->conn->prepare("
                UPDATE products 
                SET stock = stock - ? 
                WHERE product_id = ?
            ");
            $update_product->bind_param("ii", $test_quantity, $product_id);
            $update_product->execute();
            
            // Verify the update
            $verify_query = $this->conn->prepare("
                SELECT stock FROM products WHERE product_id = ?
            ");
            $verify_query->bind_param("i", $product_id);
            $verify_query->execute();
            $verify_result = $verify_query->get_result();
            $updated_product = $verify_result->fetch_assoc();
            
            $new_stock = $updated_product['stock'];
            $expected_stock = $initial_stock - $test_quantity;
            
            $this->testResults['inventory_update'] = [
                'name' => 'Inventory Update Test',
                'passed' => ($new_stock == $expected_stock),
                'data' => "Product #$product_id: Initial stock: $initial_stock, New stock: $new_stock"
            ];
            
        } catch (Exception $e) {
            $this->testResults['inventory_update'] = [
                'name' => 'Inventory Update Test',
                'passed' => false,
                'data' => "Error: " . $e->getMessage()
            ];
        }
        
        // Rollback all changes
        $this->conn->rollback();
    }
    
    // Test cart clearing (simulated)
    private function testCartClearing() {
        // Begin transaction so we can rollback after testing
        $this->conn->begin_transaction();
        
        try {
            // Get cart information
            $cart_query = $this->conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
            $cart_query->bind_param("i", $this->testUserId);
            $cart_query->execute();
            $cart_result = $cart_query->get_result();
            $cart = $cart_result->fetch_assoc();
            
            if (!$cart) {
                throw new Exception("No cart found for test user");
            }
            
            $cart_id = $cart["cart_id"];
            
            // Count items before clearing
            $count_query = $this->conn->prepare("SELECT COUNT(*) as count FROM cart_items WHERE cart_id = ?");
            $count_query->bind_param("i", $cart_id);
            $count_query->execute();
            $count_result = $count_query->get_result();
            $count_before = $count_result->fetch_assoc()['count'];
            
            // Test cart clearing
            $clear_cart = $this->conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $clear_cart->bind_param("i", $cart_id);
            $clear_cart->execute();
            
            // Count items after clearing
            $count_query->execute();
            $count_result = $count_query->get_result();
            $count_after = $count_result->fetch_assoc()['count'];
            
            $this->testResults['cart_clearing'] = [
                'name' => 'Cart Clearing Test',
                'passed' => ($count_after == 0),
                'data' => "Items before clearing: $count_before, After: $count_after"
            ];
            
        } catch (Exception $e) {
            $this->testResults['cart_clearing'] = [
                'name' => 'Cart Clearing Test',
                'passed' => false,
                'data' => "Error: " . $e->getMessage()
            ];
        }
        
        // Rollback all changes
        $this->conn->rollback();
    }
    
    // Display test results
    private function displayResults() {
        echo "<h1>Checkout Functionality Test Results</h1>";
        
        foreach ($this->testResults as $test) {
            $status = $test['passed'] ? 'PASSED' : 'FAILED';
            $color = $test['passed'] ? 'green' : 'red';
            
            echo "<div style='margin-bottom: 20px;'>";
            echo "<h3>{$test['name']}: <span style='color: $color;'>$status</span></h3>";
            echo "<p>{$test['data']}</p>";
            echo "</div>";
        }
    }
}

// Run the tests
$tester = new CheckoutTest($conn);
$tester->runAllTests();
?>