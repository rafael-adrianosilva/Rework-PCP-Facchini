<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
$eh_pagina_upload = ($pagina_atual === 'uploads.php' || strpos($pagina_atual, 'upload_') === 0);
$regiao_atual = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';

$regioes_validas = ['Votuporanga', 'Rio Preto 1', 'Rio Preto 2', 'Roseira', 'Mirassol', 'Aparecida Taboado'];
?>
<nav class="navbar">
    <div class="navbar-img">
        <img src="assets/imgs/logo_facchini.png" alt="Logo Facchini">
    </div>
    <div class="navbar-items">
        <?php
        if ($eh_pagina_upload) {
            if (!empty($regiao_atual)) {
                echo '<!-- Modo Upload: Região travada, sem dropdown -->';
                echo '<div class="region-locked-badge" title="Você está enviando arquivos para esta região">';
                echo '    <i class="fas fa-map-marker-alt"></i>';
                echo '    <span>' . htmlspecialchars($regiao_atual) . '</span>';
                echo '    <i class="fas fa-lock" style="font-size: 11px;"></i>';
                echo '</div>';
            } else {
                echo '<!-- Modo Upload sem região: Exibe aviso -->';
                echo '<div class="region-locked-badge" title="Nenhuma região foi definida">';
                echo '    <i class="fas fa-map-marker-alt"></i>';
                echo '    <span>Região não definida</span>';
                echo '    <i class="fas fa-lock" style="font-size: 11px;"></i>';
                echo '</div>';
            }
        } else {
            echo '<!-- Seletor de Região Dinâmico -->';
            echo '<div class="region-selector">';
            echo '    <button type="button" class="nav-btn" id="btn-region" title="Selecione sua Região">';
            echo '        <i class="fas fa-map-marker-alt"></i>';
            echo '        <span id="selected-region-name">' . (!empty($regiao_atual) ? htmlspecialchars($regiao_atual) : 'Região não definida') . '</span>';
            echo '        <i class="fas fa-chevron-down" style="font-size: 12px;"></i>';
            echo '    </button>';
            echo '    <div class="region-dropdown" id="region-dropdown">';
            echo '        <div class="region-option" data-region="">';
            echo '            <i class="fas fa-globe-americas" style="margin-right: 8px;"></i>Todas as Regiões';
            echo '        </div>';
            echo '        <div class="region-separator"></div>';
            
            foreach ($regioes_validas as $reg) {
                echo '        <div class="region-option" data-region="' . $reg . '">';
                echo '            ' . $reg;
                echo '        </div>';
            }
            
            echo '    </div>';
            echo '</div>';
            echo '<input type="hidden" id="regiao_selecionada" value="' . htmlspecialchars($regiao_atual) . '">';
        }
        
        echo '<button id="tema" onclick="changeTheme()"><i class="fas fa-moon"></i></button>';
        ?>
    </div>
</nav>