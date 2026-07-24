<?php
function getRecommendations($conn, $user_id, $limit = 4) {
    // Sanitize limit
    $limit = (int)$limit;
    if ($limit < 1) $limit = 4;
    
    // Get categories of products the user has purchased (using prepared statement)
    $cat_stmt = $conn->prepare("SELECT DISTINCT p.product_category FROM order_items oi 
                JOIN orders o ON oi.order_id=o.id 
                JOIN products p ON oi.product_id=p.id 
                WHERE o.user_id = ? AND o.status != 'cancelled'");
    $cat_stmt->bind_param("i", $user_id);
    $cat_stmt->execute();
    $cats = $cat_stmt->get_result();
    $categories = [];
    while ($c = $cats->fetch_assoc()) $categories[] = $c['product_category'];
    $cat_stmt->close();
    
    if (empty($categories)) {
        // New user - recommend best rated products
        return $conn->query("SELECT * FROM products ORDER BY rating_avg DESC LIMIT $limit");
    }
    
    // Get IDs of products the user has already purchased (using prepared statement)
    $prod_stmt = $conn->prepare("SELECT DISTINCT product_id FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE o.user_id = ?");
    $prod_stmt->bind_param("i", $user_id);
    $prod_stmt->execute();
    $prod_ids = $prod_stmt->get_result();
    $exclude_ids = [0];
    while ($p = $prod_ids->fetch_assoc()) $exclude_ids[] = (int)$p['product_id'];
    $prod_stmt->close();
    
    // Build category list safely
    $cat_list = "'" . implode("','", array_map(function($c) use ($conn) { return $conn->real_escape_string($c); }, $categories)) . "'";
    $id_list = implode(",", $exclude_ids);
    
    // Recommend products from same categories, excluding already purchased
    $sql = "SELECT * FROM products WHERE product_category IN ($cat_list) AND id NOT IN ($id_list) ORDER BY rating_avg DESC, RAND() LIMIT $limit";
    $result = $conn->query($sql);
    
    $final_ids = [];
    while ($r = $result->fetch_assoc()) $final_ids[] = (int)$r['id'];
    
    if (count($final_ids) < $limit) {
        // Need more products - fetch top rated excluding all collected so far
        $all_exclude = array_merge($exclude_ids, $final_ids);
        $all_exclude_str = implode(",", $all_exclude);
        $needed = $limit - count($final_ids);
        $extra = $conn->query("SELECT * FROM products WHERE id NOT IN ($all_exclude_str) ORDER BY rating_avg DESC LIMIT $needed");
        while ($r = $extra->fetch_assoc()) $final_ids[] = (int)$r['id'];
        
        if (empty($final_ids)) {
            // Fallback: return any products if nothing matched
            return $conn->query("SELECT * FROM products ORDER BY rating_avg DESC LIMIT $limit");
        }
        
        // Return a clean query with the collected IDs
        $final_ids_str = implode(",", $final_ids);
        return $conn->query("SELECT * FROM products WHERE id IN ($final_ids_str)");
    }
    
    return $result;
}
?>
