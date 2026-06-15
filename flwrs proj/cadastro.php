<?php
// cadastro.php - versão completa com validações
require_once 'config.php';

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitização dos campos
    $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma'] ?? '';
    $data_nasc = $_POST['data-nasc'] ?? null;
    $genero = $_POST['genero'] ?? null;
    $telefone = trim(filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING));
    $cep = trim(filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING));
    $numero = trim(filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING));
    $termos = isset($_POST['termos']) ? 1 : 0;
    
    // ========== VALIDAÇÕES ==========
    
    // 1. Nome
    if (empty($nome)) {
        $erros[] = "Nome completo é obrigatório";
    } elseif (strlen($nome) < 3) {
        $erros[] = "Nome deve ter pelo menos 3 caracteres";
    }
    
    // 2. Email
    if (empty($email)) {
        $erros[] = "E-mail é obrigatório";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    }
    
    // 3. Senha
    if (empty($senha)) {
        $erros[] = "Senha é obrigatória";
    } elseif (strlen($senha) < 8) {
        $erros[] = "Senha deve ter no mínimo 8 caracteres";
    }
    
    // 4. Confirmar senha
    if ($senha !== $confirma) {
        $erros[] = "As senhas não coincidem";
    }
    
    // 5. Data de nascimento (se fornecida, validar formato)
    if (!empty($data_nasc)) {
        $data_obj = DateTime::createFromFormat('Y-m-d', $data_nasc);
        if (!$data_obj || $data_obj->format('Y-m-d') !== $data_nasc) {
            $erros[] = "Data de nascimento inválida";
        } else {
            // Verificar idade mínima (opcional: 13 anos)
            $hoje = new DateTime();
            $idade = $hoje->diff($data_obj)->y;
            if ($idade < 13) {
                $erros[] = "Você deve ter pelo menos 13 anos para se cadastrar";
            }
        }
    }
    
    // 6. Gênero (se for fornecido, validar opção)
    if (!empty($genero) && !in_array($genero, ['f', 'm', 'outro'])) {
        $erros[] = "Opção de gênero inválida";
    }
    
    // 7. Termos de uso
    if (!$termos) {
        $erros[] = "Você deve aceitar os termos de uso e política de privacidade";
    }
    
    // ========== INSERÇÃO NO BANCO ==========
    
    if (empty($erros)) {
        // Usar a variável $pdo do config.php em vez de criar Database()
        global $pdo;
        
        try {
            // Verificar se e-mail já existe
            $check_sql = "SELECT id FROM usuarios WHERE email = :email";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([':email' => $email]);
            
            if ($check_stmt->rowCount() > 0) {
                $erros[] = "Este e-mail já está cadastrado. Faça login ou use outro e-mail.";
            } else {
                // Gerar hash da senha (NUNCA salvar em texto puro!)
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Preparar INSERT
                $sql = "INSERT INTO usuarios (
                            nome_completo, 
                            email, 
                            senha_hash, 
                            data_nascimento, 
                            genero, 
                            telefone, 
                            cep, 
                            numero, 
                            termos_aceitos
                        ) VALUES (
                            :nome, 
                            :email, 
                            :senha_hash, 
                            :data_nasc, 
                            :genero, 
                            :telefone, 
                            :cep, 
                            :numero, 
                            :termos
                        )";
                
                $stmt = $pdo->prepare($sql);
                
                // Executar com os parâmetros
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':senha_hash' => $senha_hash,
                    ':data_nasc' => !empty($data_nasc) ? $data_nasc : null,
                    ':genero' => !empty($genero) ? $genero : null,
                    ':telefone' => !empty($telefone) ? $telefone : null,
                    ':cep' => !empty($cep) ? $cep : null,
                    ':numero' => !empty($numero) ? $numero : null,
                    ':termos' => $termos
                ]);
                
                $sucesso = true;
                
                // Iniciar sessão e logar automaticamente
                session_start();
                $_SESSION['usuario_id'] = $pdo->lastInsertId();
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_email'] = $email;
                
                // Redirecionar após 2 segundos
                header("refresh:2; url=home.php");
            }
            
        } catch(PDOException $e) {
            $erros[] = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flwrs · criar conta</title>
  <!-- mesma fonte da home -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
  <!-- Material Symbols para ícone de olho (leve e moderno) -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1" />
  <link rel="stylesheet" href="css/cadastro.css">
  <style>
  </style>
</head>
<body>
  <header>
    <div class="container header-flex">
      <div class="header-left">
        <!-- Seta de voltar -->
        <a href="home.php" class="back-button">
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
        <h3>Criar conta</h3>
        <div class="form-sub">preencha para fazer parte</div>

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
        <?php if ($sucesso): ?>
          <div class="success-message">
            ✅ Cadastro realizado com sucesso! Redirecionando...
          </div>
        <?php endif; ?>

        <form action="#" method="POST">
          <div class="input-group">
            <label for="nome">nome completo</label>
            <input type="text" id="nome" name="nome" placeholder="" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
          </div>

          <div class="input-group">
            <label for="email">e-mail</label>
            <input type="email" id="email" name="email" placeholder="central.flwrs@gmail.com" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
          </div>

          <!-- campo senha com olho -->
          <div class="linha-dupla">
            <div class="input-group">
              <label for="senha">senha</label>
              <div class="password-wrapper">
                <input type="password" id="senha" name="senha" placeholder="mín. 8 caracteres" required minlength="8">
                <button type="button" class="toggle-password" data-target="senha" aria-label="mostrar/esconder senha">
                  <span class="material-symbols-outlined">visibility</span>
                </button>
              </div>
            </div>
            <div class="input-group">
              <label for="confirma">confirmar senha</label>
              <div class="password-wrapper">
                <input type="password" id="confirma" name="confirma" placeholder="digite novamente" required>
                <button type="button" class="toggle-password" data-target="confirma" aria-label="mostrar/esconder senha">
                  <span class="material-symbols-outlined">visibility</span>
                </button>
              </div>
            </div>
          </div>

          <div class="linha-dupla">
            <div class="input-group">
              <label for="data-nasc">data de nascimento</label>
              <input type="date" id="data-nasc" name="data-nasc" value="<?php echo htmlspecialchars($data_nasc ?? ''); ?>">
            </div>
            <div class="input-group">
              <label for="genero">gênero (opcional)</label>
              <select id="genero" name="genero">
                <option value="" <?php echo empty($genero) ? 'selected' : ''; ?>>—</option>
                <option value="f" <?php echo ($genero ?? '') === 'f' ? 'selected' : ''; ?>>feminino</option>
                <option value="m" <?php echo ($genero ?? '') === 'm' ? 'selected' : ''; ?>>masculino</option>
                <option value="outro" <?php echo ($genero ?? '') === 'outro' ? 'selected' : ''; ?>>outro</option>
              </select>
            </div>
          </div>

          <div class="input-group">
            <label for="telefone">celular / whatsapp</label>
            <input type="tel" id="telefone" name="telefone" placeholder="(45) 99999-9999" value="<?php echo htmlspecialchars($telefone ?? ''); ?>">
          </div>

          <div class="linha-dupla">
            <div class="input-group">
              <label for="cep">CEP (para entregas)</label>
              <input type="text" id="cep" name="cep" placeholder="00000-000" value="<?php echo htmlspecialchars($cep ?? ''); ?>">
            </div>
            <div class="input-group">
              <label for="numero">número</label>
              <input type="text" id="numero" name="numero" placeholder="ex: 42" value="<?php echo htmlspecialchars($numero ?? ''); ?>">
            </div>
          </div>

          <div class="termos">
            <input type="checkbox" id="termos" name="termos" <?php echo isset($termos) && $termos ? 'checked' : ''; ?> required>
            <label for="termos">aceito os <a href="#">termos de uso</a> e a <a href="#">política de privacidade</a> da Flwrs.</label>
          </div>

          <button type="submit" class="btn-cadastrar">criar minha conta</button>

          <div class="login-redirect">
            Já tem uma conta? <a href="login.php">fazer login</a>
          </div>
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

<script src="js/cadastro.js"></script>
</body>
</html>