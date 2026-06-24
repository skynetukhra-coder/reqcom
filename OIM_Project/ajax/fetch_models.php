<?php
require_once dirname(__DIR__) . "/db_connect.php";

if(isset($_GET['item_id'])){
    $item_id = trim($_GET['item_id']);

    // RECTIFICATION: Explicitly handle mapping if there is an ID mismatch in the models table
    // If 'RL' (Roll) is passed but the database references 'CAR' or 'ROLL', check for all permutations safely.
    $is_roll = ($item_id === 'RL' || strtolower($item_id) === 'roll');

    // Force parameters and CONCAT expressions to collation 'utf8mb4_general_ci' to prevent collation mix errors on Hostinger
    $sql = "SELECT m.model_id, m.model_name 
            FROM models m
            LEFT JOIN items i ON m.item_id = i.item_id
            WHERE (TRIM(m.item_id) = TRIM(? COLLATE utf8mb4_general_ci) 
               OR TRIM(i.item_name) = TRIM(? COLLATE utf8mb4_general_ci)
               OR TRIM(i.item_id) = TRIM(? COLLATE utf8mb4_general_ci)
               OR i.item_name LIKE CONCAT('%', ?, '%') COLLATE utf8mb4_general_ci
               OR m.item_id LIKE CONCAT('%', ?, '%') COLLATE utf8mb4_general_ci)";

    // If it's a Roll item, append an optional condition to capture models mismatched under 'CAR' or 'RL'
    if ($is_roll) {
        $sql .= " AND (m.model_name LIKE '%Roll%' OR i.item_name LIKE '%Roll%' OR m.item_id = 'RL')";
    } else {
        // If it's NOT a roll (e.g. true Cartridge request), explicitly EXCLUDE roll models from leaking into it
        $sql .= " AND m.model_name NOT LIKE '%Roll%' AND i.item_name NOT LIKE '%Roll%'";
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $item_id, $item_id, $item_id, $item_id, $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } catch (Exception $e) {
        echo "<option value=''>QUERY_ERROR: " . htmlspecialchars($e->getMessage()) . "</option>";
        exit();
    }

    echo "<option value=''>-- Select Model --</option>";

    if($result && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<option value='".htmlspecialchars($row['model_id'])."'>"
                .htmlspecialchars($row['model_name']).
                "</option>";
        }
    } else {
        echo "<option value=''>No models found</option>";
    }
}
?>