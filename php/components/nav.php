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
            <!-- Seletor de Região Dinâmico -->
            <div class="region-selector">
                <button type="button" class="nav-btn" id="btn-region" title="Selecione sua Região">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="selected-region-name"><?php echo !empty($regiao_atual) ? htmlspecialchars($regiao_atual) : 'Região não definida'; ?></span>
                    <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                </button>
                <div class="region-dropdown" id="region-dropdown">
                    <div class="region-option" data-region="">
                        <i class="fas fa-globe-americas" style="margin-right: 8px;"></i>Todas as Regiões
                    </div>
                    <div class="region-separator"></div>
                    <?php foreach ($regioes_validas as $reg): ?>
                        <div class="region-option" data-region="<?php echo $reg; ?>">
                            <?php echo $reg; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" id="regiao_selecionada" value="<?php echo htmlspecialchars($regiao_atual); ?>">
        <?php endif; ?>
        <button id="tema" onclick="changeTheme()"><i class="fas fa-moon"></i></button>
    </div>
</nav>