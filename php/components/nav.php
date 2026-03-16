<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
$eh_pagina_upload = ($pagina_atual === 'uploads.php');
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
                <div class="region-locked-badge region-locked-warning" title="Nenhuma região foi definida na URL">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Região não definida</span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Modo Visualização: Dropdown normal para filtrar -->
            <div class="region-selector">
                <button id="btn-region" class="nav-btn" title="Selecionar Região para Visualizar">
                    <i class="fas fa-globe"></i>
                    <span id="selected-region-name"><?php echo !empty($regiao_atual) ? htmlspecialchars($regiao_atual) : 'Todas as Regiões'; ?></span>
                </button>
                <div class="region-dropdown" id="region-dropdown">
                    <div class="region-option" data-region="">Todas as Regiões</div>
                    <div class="region-separator"></div>
                    <?php foreach ($regioes_validas as $regiao): ?>
                    <div class="region-option" data-region="<?php echo htmlspecialchars($regiao); ?>"><?php echo htmlspecialchars($regiao); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <button id="tema" onclick="changeTheme()"><i class="fas fa-moon"></i></button>
    </div>
</nav>