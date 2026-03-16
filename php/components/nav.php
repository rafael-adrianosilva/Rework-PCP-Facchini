<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
$eh_pagina_upload = ($pagina_atual === 'uploads.php' || strpos($pagina_atual, 'upload_') === 0);
$regiao_atual = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';

// Lista de regiões válidas (única fonte de verdade)
$regioes_validas = ['Votuporanga', 'Rio Preto 1', 'Rio Preto 2', 'Roseira', 'Mirassol', 'Aparecida Taboado'];
?>
<nav class="navbar">
    <div class="navbar-img">
        <img onclick="trocarPagina()" src="assets/imgs/logo_facchini.png" alt="Logo Facchini">
    </div>
    <div class="navbar-items">
        <?php if ($eh_pagina_upload): ?>
            <?php if (!empty($regiao_atual)): ?>
                <!-- Modo Upload: Região travada, sem dropdown -->
                <div class="region-locked-badge" title="Você está enviando arquivos para esta região">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($regiao_atual); ?></span>
                    <i class="fas fa-lock" style="font-size: 11px;"></i>
                </div>
            <?php else: ?>
                <!-- Modo Upload sem região: Exibe aviso -->
                <div class="region-locked-badge region-locked-warning" title="Nenhuma região foi definida">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Região não definida</span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Sem seletor de região na visualização (removido conforme solicitado) -->
        <?php endif; ?>
        <button id="tema" onclick="changeTheme()"><i class="fas fa-moon"></i></button>
    </div>
</nav>