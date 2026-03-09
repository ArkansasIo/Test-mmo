<?php
/**
 * Marketplace Page - Trade resources with other players
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$playerData = $player->getData();
$db = Database::getInstance();

// Get active market orders
$marketOrders = $db->fetchAll("SELECT m.*, p.username FROM market_orders m 
                               JOIN players p ON m.player_id = p.id 
                               WHERE m.status = 'active' AND m.player_id != ? 
                               ORDER BY m.created_at DESC LIMIT 50", [$player->getId()]);

// Get player's orders
$myOrders = $db->fetchAll("SELECT * FROM market_orders WHERE player_id = ? ORDER BY created_at DESC", [$player->getId()]);
?>

<div class="marketplace-page">
    <div class="page-header">
        <h1>Marketplace</h1>
        <p>Trade resources with other players</p>
    </div>
    
    <!-- Create Order -->
    <div class="create-order" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Create Trade Order</h2>
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <h3 style="color: #4a9eff; margin-bottom: 10px;">Offering</h3>
                    <div style="margin-bottom: 10px;">
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Resource</label>
                        <select name="offer_resource" required 
                                style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                            <option value="metal">Metal</option>
                            <option value="crystal">Crystal</option>
                            <option value="deuterium">Deuterium</option>
                        </select>
                    </div>
                    <div>
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Amount</label>
                        <input type="number" name="offer_amount" min="1" required 
                               style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    </div>
                </div>
                
                <div>
                    <h3 style="color: #4a9eff; margin-bottom: 10px;">Requesting</h3>
                    <div style="margin-bottom: 10px;">
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Resource</label>
                        <select name="request_resource" required 
                                style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                            <option value="metal">Metal</option>
                            <option value="crystal">Crystal</option>
                            <option value="deuterium">Deuterium</option>
                        </select>
                    </div>
                    <div>
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Amount</label>
                        <input type="number" name="request_amount" min="1" required 
                               style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    </div>
                </div>
            </div>
            
            <button type="submit" name="create_order" class="btn" style="width: 100%;">
                Create Trade Order
            </button>
        </form>
    </div>
    
    <!-- My Orders -->
    <?php if (!empty($myOrders)): ?>
    <div class="my-orders" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">My Orders</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: rgba(74, 158, 255, 0.2);">
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Offering</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Requesting</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Status</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Created</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myOrders as $order): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo number_format($order['offer_amount']); ?> <?php echo ucfirst($order['offer_resource']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo number_format($order['request_amount']); ?> <?php echo ucfirst($order['request_resource']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo ucfirst($order['status']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo date('Y-m-d H:i', $order['created_at']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">
                        <?php if ($order['status'] == 'active'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="cancel_order" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">Cancel</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Market Orders -->
    <div class="market-orders" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Available Orders</h2>
        <?php if (!empty($marketOrders)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: rgba(74, 158, 255, 0.2);">
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Trader</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Offering</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Requesting</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Rate</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marketOrders as $order): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo htmlspecialchars($order['username']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo number_format($order['offer_amount']); ?> <?php echo ucfirst($order['offer_resource']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo number_format($order['request_amount']); ?> <?php echo ucfirst($order['request_resource']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo number_format($order['request_amount'] / $order['offer_amount'], 2); ?>:1</td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="accept_order" class="btn" style="padding: 5px 10px; font-size: 12px;">Accept</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color: #aaa; text-align: center;">No orders available</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Handle create order
if (isset($_POST['create_order'])) {
    $offerResource = $_POST['offer_resource'];
    $offerAmount = $_POST['offer_amount'];
    $requestResource = $_POST['request_resource'];
    $requestAmount = $_POST['request_amount'];
    
    // Check if player has resources
    $playerData = $player->getData();
    if ($playerData[$offerResource] >= $offerAmount) {
        // Deduct resources
        $player->updateResources(
            $offerResource == 'metal' ? -$offerAmount : 0,
            $offerResource == 'crystal' ? -$offerAmount : 0,
            $offerResource == 'deuterium' ? -$offerAmount : 0
        );
        
        // Create order
        $db->insert('market_orders', [
            'player_id' => $player->getId(),
            'offer_resource' => $offerResource,
            'offer_amount' => $offerAmount,
            'request_resource' => $requestResource,
            'request_amount' => $requestAmount,
            'status' => 'active',
            'created_at' => time()
        ]);
        
        echo "<script>alert('Trade order created!'); window.location.href='?page=marketplace';</script>";
    } else {
        echo "<script>alert('Not enough resources!');</script>";
    }
}

// Handle accept order
if (isset($_POST['accept_order'])) {
    $orderId = $_POST['order_id'];
    $order = $db->fetchOne("SELECT * FROM market_orders WHERE id = ?", [$orderId]);
    
    if ($order && $order['status'] == 'active') {
        $playerData = $player->getData();
        if ($playerData[$order['request_resource']] >= $order['request_amount']) {
            // Execute trade
            $db->beginTransaction();
            
            // Update buyer (current player)
            $player->updateResources(
                $order['offer_resource'] == 'metal' ? $order['offer_amount'] : ($order['request_resource'] == 'metal' ? -$order['request_amount'] : 0),
                $order['offer_resource'] == 'crystal' ? $order['offer_amount'] : ($order['request_resource'] == 'crystal' ? -$order['request_amount'] : 0),
                $order['offer_resource'] == 'deuterium' ? $order['offer_amount'] : ($order['request_resource'] == 'deuterium' ? -$order['request_amount'] : 0)
            );
            
            // Update seller
            $seller = new Player($order['player_id']);
            $seller->updateResources(
                $order['request_resource'] == 'metal' ? $order['request_amount'] : 0,
                $order['request_resource'] == 'crystal' ? $order['request_amount'] : 0,
                $order['request_resource'] == 'deuterium' ? $order['request_amount'] : 0
            );
            
            // Mark order as completed
            $db->update('market_orders', ['status' => 'completed'], 'id = :id', ['id' => $orderId]);
            
            $db->commit();
            
            echo "<script>alert('Trade completed!'); window.location.href='?page=marketplace';</script>";
        } else {
            echo "<script>alert('Not enough resources!');</script>";
        }
    }
}

// Handle cancel order
if (isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'];
    $order = $db->fetchOne("SELECT * FROM market_orders WHERE id = ? AND player_id = ?", [$orderId, $player->getId()]);
    
    if ($order) {
        // Refund resources
        $player->updateResources(
            $order['offer_resource'] == 'metal' ? $order['offer_amount'] : 0,
            $order['offer_resource'] == 'crystal' ? $order['offer_amount'] : 0,
            $order['offer_resource'] == 'deuterium' ? $order['offer_amount'] : 0
        );
        
        // Mark order as cancelled
        $db->update('market_orders', ['status' => 'cancelled'], 'id = :id', ['id' => $orderId]);
        
        echo "<script>alert('Order cancelled!'); window.location.href='?page=marketplace';</script>";
    }
}
?>
