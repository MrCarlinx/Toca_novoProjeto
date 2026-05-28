<?php
require_once __DIR__ . '/includes/check_admin.php';

$action = $_GET['action'] ?? 'list';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $nome = $_POST['nome'];
        $id_categoria = $_POST['id_categoria'];
        $preco = $_POST['preco'];
        $badge = $_POST['badge'] ?: null;
        $descricao = $_POST['descricao'];
        $img_url = $_POST['img_url'] ?: 'img/produtos/placeholder.jpg';
        
        $stmt = $pdo->prepare("INSERT INTO produtos (id_categoria, nome, descricao, preco, badge, img_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_categoria, $nome, $descricao, $preco, $badge, $img_url]);
        $msg = "Produto adicionado!";
        $action = 'list';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $id_categoria = $_POST['id_categoria'];
        $preco = $_POST['preco'];
        $badge = $_POST['badge'] ?: null;
        $descricao = $_POST['descricao'];
        $img_url = $_POST['img_url'] ?: 'img/produtos/placeholder.jpg';
        
        $stmt = $pdo->prepare("UPDATE produtos SET id_categoria=?, nome=?, descricao=?, preco=?, badge=?, img_url=? WHERE id=?");
        $stmt->execute([$id_categoria, $nome, $descricao, $preco, $badge, $img_url, $id]);
        $msg = "Produto atualizado!";
        $action = 'list';
    }
}

if ($action === 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id=?");
    $stmt->execute([$id]);
    $msg = "Produto excluído!";
    $action = 'list';
}

$page_title = 'Produtos - Admin';
require_once __DIR__ . '/../includes/head.php';

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
?>

<div class="flex h-screen bg-surface-container-low">
  <!-- Sidebar -->
  <aside class="w-64 bg-primary-container text-on-primary-container flex flex-col">
    <div class="p-6">
      <h2 class="font-headline-md text-white font-bold">Admin Toca</h2>
    </div>
    <nav class="flex-1 px-4 space-y-2">
      <a href="index.php" class="block py-3 px-4 rounded-lg hover:bg-primary/10 transition-colors">Dashboard</a>
      <a href="produtos.php" class="block py-3 px-4 rounded-lg bg-primary/20 text-white font-bold">Produtos</a>
      <a href="pedidos.php" class="block py-3 px-4 rounded-lg hover:bg-primary/10 transition-colors">Pedidos</a>
      <a href="clientes.php" class="block py-3 px-4 rounded-lg hover:bg-primary/10 transition-colors">Clientes</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8 overflow-y-auto">
    <header class="flex justify-between items-center mb-10">
      <h1 class="font-headline-xl text-primary">Gerenciar Produtos</h1>
      <?php if($action === 'list'): ?>
      <a href="?action=add" class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-md">Novo Produto</a>
      <?php endif; ?>
    </header>

    <?php if($msg): ?>
    <div class="bg-emerald-100 text-emerald-800 p-4 rounded-lg mb-6">
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <?php if($action === 'list'): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-lowest border-b border-outline-variant/30">
                    <tr>
                        <th class="p-4 font-label-md text-secondary">ID</th>
                        <th class="p-4 font-label-md text-secondary">Nome</th>
                        <th class="p-4 font-label-md text-secondary">Categoria</th>
                        <th class="p-4 font-label-md text-secondary">Preço</th>
                        <th class="p-4 font-label-md text-secondary">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $produtos = $pdo->query("SELECT p.*, c.nome as cat FROM produtos p JOIN categorias c ON p.id_categoria = c.id ORDER BY p.id DESC")->fetchAll();
                    foreach($produtos as $p):
                    ?>
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-lowest/50">
                        <td class="p-4 text-sm"><?= $p['id'] ?></td>
                        <td class="p-4 text-sm font-bold text-primary"><?= htmlspecialchars($p['nome']) ?></td>
                        <td class="p-4 text-sm"><?= htmlspecialchars($p['cat']) ?></td>
                        <td class="p-4 text-sm">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td class="p-4 text-sm">
                            <a href="?action=edit&id=<?= $p['id'] ?>" class="text-primary hover:underline mr-3">Editar</a>
                            <a href="?action=delete&id=<?= $p['id'] ?>" class="text-error hover:underline" onclick="return confirm('Excluir produto?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif($action === 'add' || $action === 'edit'): 
        $prod = null;
        if($action === 'edit') {
            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $prod = $stmt->fetch();
        }
    ?>
        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant/30 p-8 max-w-2xl">
            <form method="POST" action="?action=<?= $action ?>">
                <?php if($prod): ?>
                    <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                <?php endif; ?>
                
                <div class="space-y-6">
                    <div>
                        <label class="block font-label-md text-secondary mb-2">Nome</label>
                        <input type="text" name="nome" required value="<?= $prod['nome'] ?? '' ?>" class="w-full border rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block font-label-md text-secondary mb-2">Categoria</label>
                        <select name="id_categoria" required class="w-full border rounded-lg px-4 py-2">
                            <?php foreach($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($prod['id_categoria']??'') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-label-md text-secondary mb-2">Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" required value="<?= $prod['preco'] ?? '' ?>" class="w-full border rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block font-label-md text-secondary mb-2">Badge (ex: OFERTA)</label>
                            <input type="text" name="badge" value="<?= $prod['badge'] ?? '' ?>" class="w-full border rounded-lg px-4 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block font-label-md text-secondary mb-2">URL da Imagem</label>
                        <input type="text" name="img_url" value="<?= $prod['img_url'] ?? 'img/produtos/placeholder.jpg' ?>" class="w-full border rounded-lg px-4 py-2">
                        <p class="text-xs text-secondary mt-1">Ex: img/produtos/prod_123.jpg</p>
                    </div>
                    <div>
                        <label class="block font-label-md text-secondary mb-2">Descrição</label>
                        <textarea name="descricao" rows="4" required class="w-full border rounded-lg px-4 py-2"><?= $prod['descricao'] ?? '' ?></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-4">
                        <a href="produtos.php" class="px-6 py-2 rounded-full font-label-md text-secondary hover:bg-surface-container-low">Cancelar</a>
                        <button type="submit" class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-md shadow-md hover:bg-primary/90">
                            Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

  </main>
</div>
</body>
</html>
