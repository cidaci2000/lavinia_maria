<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flwrs · cadastro de produtos</title>
  <!-- mesma fonte da home -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
  <!-- Material Symbols para ícone de olho (leve e moderno) -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1" />
  <link rel="stylesheet" href="css/cad_produto.css">
  <style>
  </style>
</head>
<body>
  <header>
    <div class="container header-flex">
      <div class="header-left">
        <!-- Seta de voltar -->
        <a href="admin.php" class="back-button">
          <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <!-- Logo -->
        <div class="logo-area">
          <div class="logo-word">
            Flwrs <strong>·</strong>
          </div>
          <div class="tagline-header">
            “Flowers that feel like felling”
          </div>
        </div>
      </div>
      <nav class="nav-menu">
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="cadastro-section">
      <div class="form-cadastro">
        <h3>Cadastrar produtos</h3>
        <div class="form-sub">preencha para cadastrar seu produto</div>

        <!-- Exibir mensagens de erro -->
        <?php if (!empty($erros)): ?>
          <div class="error-messages">
            <ul>
              <?php foreach ($erros as $erro): ?>
                <li><?php echo htmlspecialchars($erro); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Exibir mensagem de sucesso -->
        <!-- <?php if ($sucesso): ?>
          <div class="success-message">
            ✅ Cadastro realizado com sucesso! Redirecionando...
          </div>
        <?php endif; ?> -->

        <form action="#" method="POST">
            <div class="input-group">
                <label for="nome">nome do produto</label>
                <input type="text" id="nome" name="nome" placeholder="" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
            </div>

            <div class="input-group">
                <label for="desc">descrição</label>
                <input type="desc" id="desc" name="desc" placeholder="" value="<?php echo htmlspecialchars($desc ?? ''); ?>" required>
            </div>

            <div class="linha-dupla">
                <div class="input-group">
                <label for="modelo">modelo</label>
                <select id="modelo" name="modelo">
                    <option value="" <?php echo empty($modelo) ? 'selected' : ''; ?>>—</option>
                    <option value="f" <?php echo ($modelo ?? '') === 'ba' ? 'selected' : ''; ?>>buquê afetivo</option>
                    <option value="m" <?php echo ($modelo ?? '') === 'a' ? 'selected' : ''; ?>>arranjo</option>
                    <option value="outro" <?php echo ($modelo ?? '') === 'pe' ? 'selected' : ''; ?>>presente especial</option>
                    <option value="outro" <?php echo ($modelo ?? '') === 'be' ? 'selected' : ''; ?>>buquê especial</option>
                    <option value="outro" <?php echo ($modelo ?? '') === 'pf' ? 'selected' : ''; ?>>palavras em flor</option>
                </select>
                </div>
            </div>

            <div class="input-group">
                <label for="preco">preço</label>
                <input type="tel" id="preco" name="preco" placeholder="R$00,00" value="<?php echo htmlspecialchars($preco ?? ''); ?>">
            </div>

            <button type="submit" class="btn-cadastrar">cadastrar produto</button>

        </form>
      </div>
    </section>

    <div class="info-rodape">
      <span>🚚 FAQ de delivery — atualizado</span>
      <span>📮 central.flwrs@gmail.com</span>
    </div>
  </main>

  <footer>
    <p>Flwrs — <span>“Flowers that feel like felling”</span> — pequenos gestos, memórias eternas</p>
  </footer>

<script src="js/cad_produto.js"></script>
</body>
</html>