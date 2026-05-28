<?php
// includes/header.php
// Parâmetro: $active_page — string com a página ativa ('inicio','catalogo','promocoes','historia','contato')
$active_page = $active_page ?? 'inicio';

$nav_links = [
    'inicio'    => ['label' => 'Início',        'href' => SITE_URL . '/index.php'],
    'catalogo'  => ['label' => 'Produtos',      'href' => page('catalogo.php')],
    'promocoes' => ['label' => 'Promoções',     'href' => page('promocoes.php')],
    'historia'  => ['label' => 'Nossa História','href' => page('sobre.php')],
    'contato'   => ['label' => 'Contato',       'href' => page('contato.php')],
];
?>
<header class="bg-surface-container-lowest shadow-sm sticky top-0 z-50">
  <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
    <div class="text-2xl font-bold text-primary">
      <a href="<?= $nav_links['inicio']['href'] ?>" style="text-decoration:none;color:inherit;">Toca do Coelho</a>
    </div>

    <nav class="main-site-menu flex gap-8 items-center">
      <?php foreach ($nav_links as $key => $link): ?>
      <a class="text-sm font-semibold <?= $active_page === $key
          ? 'text-primary border-b-2 border-primary pb-1'
          : 'text-on-surface-variant hover:text-primary transition-colors duration-200' ?>"
         href="<?= $link['href'] ?>">
        <?= $link['label'] ?>
      </a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center gap-4">
      <div class="relative hidden lg:block">
        <input class="bg-surface-container-low border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary w-64"
               id="header-search" placeholder="Buscar produtos..." type="text"/>
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
      </div>

      <a href="<?= page('carrinho.php') ?>"
         class="relative flex items-center justify-center hover:opacity-80 transition-opacity"
         title="Carrinho">
        <span class="material-symbols-outlined text-primary text-[24px]">shopping_cart</span>
        <?php
        $cartCount = isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;
        if ($cartCount > 0): ?>
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-tertiary text-on-tertiary text-[10px] font-bold rounded-full flex items-center justify-center">
          <?= $cartCount ?>
        </span>
        <?php endif; ?>
      </a>

      <?php if (isset($_SESSION['cliente_id'])): ?>
      <div class="relative group">
        <button class="flex items-center gap-1 hover:opacity-80 transition-opacity">
          <span class="material-symbols-outlined text-primary text-[24px]">person</span>
          <span class="text-sm font-semibold text-primary hidden md:inline">
            <?= htmlspecialchars(explode(' ', trim($_SESSION['cliente_nome']))[0]) ?>
          </span>
        </button>
        <div class="absolute right-0 top-full mt-2 w-48 bg-white shadow-xl rounded-lg border border-outline-variant/20 hidden group-hover:block overflow-hidden z-50">
          <?php if ($_SESSION['cliente_role'] === 'admin'): ?>
          <a href="<?= SITE_URL ?>/admin/index.php"
             class="block px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors border-b border-outline-variant/10">
            Painel Admin
          </a>
          <?php endif; ?>
          <a href="<?= page('logout.php') ?>"
             class="block px-4 py-3 text-sm text-error hover:bg-red-50 transition-colors">
            Sair
          </a>
        </div>
      </div>
      <?php else: ?>
      <a href="<?= page('login.php') ?>"
         class="flex items-center justify-center hover:opacity-80 transition-opacity"
         title="Entrar">
        <span class="material-symbols-outlined text-primary text-[24px]">login</span>
      </a>
      <?php endif; ?>

      <a class="bg-tertiary text-on-tertiary px-6 py-2 rounded-full text-sm font-bold flex items-center gap-2 hover:opacity-90 transition-all"
         href="<?= whatsapp_link('Olá, gostaria de mais informações sobre os produtos da Toca do Coelho.') ?>"
         target="_blank" style="text-decoration: none">
        <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">chat</span>
        WhatsApp
      </a>
    </div>
  </div>
</header>

<script>
(function () {
  const inp = document.getElementById('header-search');
  if (!inp) return;
  inp.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && this.value.trim()) {
      window.location.href = window.SITE_URL + '/pages/catalogo.php?q=' + encodeURIComponent(this.value.trim());
    }
  });
})();
</script>
