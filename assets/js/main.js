// assets/js/main.js
// Funcionalidades globais do site Toca do Coelho

document.addEventListener('DOMContentLoaded', () => {

    // ── Carrinho (Agora via AJAX conectado à Sessão) ──────────────────────
    function updateCartBadge() {
        // Busca do servidor quantos itens há no carrinho
        fetch(window.SITE_URL + '/ajax/cart_actions.php?action=get_cart')
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const cart = data.cart || {};
                    let total = 0;
                    for (const id in cart) {
                        total += cart[id];
                    }
                    document.querySelectorAll('[data-cart-count]').forEach(el => {
                        el.textContent = total;
                        el.style.display = total > 0 ? 'flex' : 'none';
                    });
                }
            });
    }

    // Adicionar ao carrinho via botão data-add-cart
    document.querySelectorAll('[data-add-cart]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.addCart;
            
            // Envia para o backend (PHP Session)
            fetch(window.SITE_URL + '/ajax/cart_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    updateCartBadge();
                    btn.textContent = '✓ Adicionado!';
                    setTimeout(() => btn.textContent = btn.dataset.label || 'Adicionar', 1500);
                }
            });
        });
    });

    // Atualiza a badge no carregamento da página
    updateCartBadge();

    // ── Busca simples (filtragem de cards na página de catálogo) ──────────
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            document.querySelectorAll('[data-product-card]').forEach(card => {
                const text = card.dataset.productCard.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

});
