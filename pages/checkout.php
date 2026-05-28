<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['cliente_id']) || empty($_SESSION['carrinho'])) {
    header('Location: ' . page('carrinho.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $cliente_id = $_SESSION['cliente_id'];
        
        // Calcular total
        $ids = implode(',', array_map('intval', array_keys($_SESSION['carrinho'])));
        $stmt = $pdo->query("SELECT id, nome, preco, preco_promocional FROM produtos WHERE id IN ($ids)");
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = 0;
        $itens_db = [];
        $itens_msg = [];
        
        foreach ($produtos as $p) {
            $qty = $_SESSION['carrinho'][$p['id']];
            $preco = $p['preco_promocional'] ?: $p['preco'];
            $total += $preco * $qty;
            
            $itens_db[] = [
                'id' => $p['id'],
                'qty' => $qty,
                'preco' => $preco
            ];
            
            $itens_msg[] = "{$qty}x {$p['nome']} (R$ " . number_format($preco, 2, ',', '.') . ")";
        }
        
        // Inserir pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, total, status) VALUES (?, ?, 'pendente')");
        $stmt->execute([$cliente_id, $total]);
        $pedido_id = $pdo->lastInsertId();
        
        // Inserir itens
        $stmt_item = $pdo->prepare("INSERT INTO pedido_itens (id_pedido, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        foreach ($itens_db as $item) {
            $stmt_item->execute([$pedido_id, $item['id'], $item['qty'], $item['preco']]);
        }
        
        $pdo->commit();
        
        // Limpar carrinho
        $_SESSION['carrinho'] = [];
        
        // Buscar dados do cliente
        $stmt = $pdo->prepare("SELECT nome FROM clientes WHERE id = ?");
        $stmt->execute([$cliente_id]);
        $cliente = $stmt->fetch();
        
        // Gerar mensagem WhatsApp
        $msg = "Olá! Gostaria de finalizar meu pedido (#{$pedido_id}) pelo site.\n\n";
        $msg .= "Cliente: {$cliente['nome']}\n\n";
        $msg .= "Itens do Pedido:\n" . implode("\n", $itens_msg) . "\n\n";
        $msg .= "Total Estimado: R$ " . number_format($total, 2, ',', '.');
        
        $wa_link = whatsapp_link($msg);
        
        // Redirecionar
        header('Location: ' . $wa_link);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao processar pedido: " . $e->getMessage());
    }
} else {
    header('Location: ' . page('carrinho.php'));
    exit;
}
