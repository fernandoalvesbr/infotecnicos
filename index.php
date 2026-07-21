<?php
// Inicia a sessão com um nome exclusivo para evitar conflito com outros projetos
if (session_status() == PHP_SESSION_NONE) {
    session_name('SISTEMA_DOCS_TECNICOS');
    session_start();
}

// Configurações de ficheiros
$arquivoJson = __DIR__ . '/data.json';
$arquivoUsuariosJson = __DIR__ . '/users.json';
$diretorioUploads = __DIR__ . '/uploads/';

// 1. Cria a estrutura necessária caso não exista
if (!is_dir($diretorioUploads)) {
    @mkdir($diretorioUploads, 0777, true);
}
$indexUploads = $diretorioUploads . 'index.php';
if (!file_exists($indexUploads)) {
    @file_put_contents($indexUploads, '<?php // Acesso negado ?>');
}
if (!file_exists($arquivoJson)) {
    @file_put_contents($arquivoJson, json_encode(array()));
}

// 2. Inicializa o ficheiro de utilizadores com o 'admin' padrão caso não exista
if (!file_exists($arquivoUsuariosJson)) {
    $usuarioPadrao = array(
        array(
            'username' => 'admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT)
        )
    );
    @file_put_contents($arquivoUsuariosJson, json_encode($usuarioPadrao));
}

// Carrega os dados dos ficheiros JSON
$funcionarios = json_decode(@file_get_contents($arquivoJson), true);
if (!is_array($funcionarios)) $funcionarios = array();

$usuarios = json_decode(@file_get_contents($arquivoUsuariosJson), true);
if (!is_array($usuarios)) $usuarios = array();


// --- LOGIC: LOGOUT ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- LOGIC: LOGIN ---
$erroLogin = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_login'])) {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    foreach ($usuarios as $u) {
        if ($u['username'] === $user_input && password_verify($pass_input, $u['password'])) {
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = $u['username'];
            header("Location: index.php");
            exit;
        }
    }
    $erroLogin = 'Utilizador ou palavra-passe incorretos.';
}

// Ecrã de Login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-PT">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Controle de Documentos</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script>
            // Carrega o tema imediatamente para evitar flash na tela de login
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        </script>
        <style>
            :root {
                --bg-color: #121212;
                --text-color: #e0e0e0;
                --card-bg: #1e1e1e;
                --input-bg: #333;
                --input-border: #444;
                --text-muted: #aaa;
                --text-strong: #fff;
                --shadow: rgba(0,0,0,0.5);
            }
            :root.light-mode {
                --bg-color: #f0f2f5;
                --text-color: #333333;
                --card-bg: #ffffff;
                --input-bg: #ffffff;
                --input-border: #cccccc;
                --text-muted: #666666;
                --text-strong: #000000;
                --shadow: rgba(0,0,0,0.1);
            }

            body { background-color: var(--bg-color); color: var(--text-color); font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; transition: background-color 0.3s; }
            .login-card { background-color: var(--card-bg); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px var(--shadow); width: 320px; position: relative; }
            h2 { margin-top: 0; color: var(--text-strong); text-align: center; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-size: 14px; color: var(--text-muted); }
            input { width: 100%; padding: 10px; background-color: var(--input-bg); border: 1px solid var(--input-border); color: var(--text-color); border-radius: 4px; box-sizing: border-box; }
            .btn { width: 100%; padding: 10px; background-color: #2e7d32; border: none; color: white; font-weight: bold; border-radius: 4px; cursor: pointer; margin-top: 10px; }
            .btn:hover { background-color: #388e3c; }
            .btn-theme-login { position: absolute; top: 15px; right: 15px; width: 35px; height: 35px; padding: 0; border-radius: 50%; background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border); cursor: pointer; display: flex; align-items: center; justify-content: center; }
            .erro { color: #f44336; font-size: 13px; text-align: center; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="login-card">
            <button class="btn-theme-login" onclick="toggleTheme()" title="Alternar Tema">
                <i class="fa fa-sun" id="themeIconLogin"></i>
            </button>
            <h2>Acesso ao Sistema</h2>
            <?php if(!empty($erroLogin)): ?>
                <div class="erro"><?php echo $erroLogin; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="acao_login" value="1">
                <div class="form-group">
                    <label>Utilizador</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Palavra-passe</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn">Entrar</button>
            </form>
        </div>
        <script>
            function toggleTheme() {
                const root = document.documentElement;
                root.classList.toggle('light-mode');
                const isLight = root.classList.contains('light-mode');
                localStorage.setItem('theme', isLight ? 'light' : 'dark');
                document.getElementById('themeIconLogin').className = isLight ? 'fa fa-moon' : 'fa fa-sun';
            }
            window.addEventListener('DOMContentLoaded', () => {
                if (localStorage.getItem('theme') === 'light') {
                    document.getElementById('themeIconLogin').className = 'fa fa-moon';
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// --- EXPORTAÇÃO: PLANILHA COMPATÍVEL COM EXCEL (CSV UTF-8) ---
// O CSV utiliza ponto e vírgula, formato normalmente reconhecido pelo Excel em PT-BR.
if (isset($_GET['action']) && $_GET['action'] === 'exportar_excel') {
    $nomeArquivo = 'tecnicos_' . date('Y-m-d_H-i-s') . '.csv';

    // Evita qualquer conteúdo antes do arquivo baixado.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // BOM UTF-8: permite que o Excel mostre acentos corretamente.
    echo "\xEF\xBB\xBF";

    $saida = fopen('php://output', 'w');
    fputcsv($saida, array(
        'Nome Completo',
        'RG',
        'CPF',
        'E-mail Pessoal',
        'Telefone Empresarial',
        'Telefone de Contacto',
        'Contacto de Emergência',
        'Telefone de Emergência',
        'Tipo de Técnico',
        'Documento ASO',
        'Documento NR',
        'Documento CNH'
    ), ';');

    foreach ($funcionarios as $func) {
        fputcsv($saida, array(
            isset($func['nome']) ? $func['nome'] : '',
            isset($func['rg']) ? $func['rg'] : '',
            isset($func['cpf']) ? $func['cpf'] : '',
            isset($func['email_pessoal']) ? $func['email_pessoal'] : '',
            isset($func['tel_empresarial']) ? $func['tel_empresarial'] : '',
            isset($func['tel_contato']) ? $func['tel_contato'] : '',
            isset($func['contato_emergencia']) ? $func['contato_emergencia'] : '',
            isset($func['tel_emergencia']) ? $func['tel_emergencia'] : '',
            isset($func['tipo_tecnico']) ? $func['tipo_tecnico'] : '',
            isset($func['arquivo_aso']) ? $func['arquivo_aso'] : '',
            isset($func['arquivo_nr']) ? $func['arquivo_nr'] : '',
            isset($func['arquivo_cnh']) ? $func['arquivo_cnh'] : ''
        ), ';');
    }

    fclose($saida);
    exit;
}


// --- FUNÇÃO: PROCESSAR UPLOAD WEBP ---
function processarDocumento($filePost, $prefixo, $id, $diretorioUploads) {
    if (!isset($filePost) || $filePost['error'] !== UPLOAD_ERR_OK) return null;
    $extensaoOriginal = strtolower(pathinfo($filePost['name'], PATHINFO_EXTENSION));
    
    if ($extensaoOriginal === 'pdf') {
        $nomeFinal = $prefixo . '_' . $id . '.pdf';
        if (move_uploaded_file($filePost['tmp_name'], $diretorioUploads . $nomeFinal)) return $nomeFinal;
    } else {
        $conteudoImagem = file_get_contents($filePost['tmp_name']);
        $imagemGerada = @imagecreatefromstring($conteudoImagem);
        if ($imagemGerada !== false) {
            $nomeFinal = $prefixo . '_' . $id . '.webp';
            if (imagewebp($imagemGerada, $diretorioUploads . $nomeFinal, 80)) {
                imagedestroy($imagemGerada);
                return $nomeFinal;
            }
            imagedestroy($imagemGerada);
        }
        $nomeFinal = $prefixo . '_' . $id . '.' . $extensaoOriginal;
        if (move_uploaded_file($filePost['tmp_name'], $diretorioUploads . $nomeFinal)) return $nomeFinal;
    }
    return null;
}


// --- PROCESSAMENTO DE AÇÕES POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';

    // Ação: Mudar Minha Palavra-passe
    if ($acao === 'mudar_minha_senha') {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        
        foreach ($usuarios as $key => $u) {
            if ($u['username'] === $_SESSION['usuario']) {
                if (password_verify($senha_atual, $u['password'])) {
                    $usuarios[$key]['password'] = password_hash($nova_senha, PASSWORD_DEFAULT);
                    file_put_contents($arquivoUsuariosJson, json_encode($usuarios));
                }
                break;
            }
        }
        header("Location: index.php");
        exit;
    }

    // Ação: Importar CSV
    if ($acao === 'importar_csv') {
        if (isset($_FILES['arquivo_csv']) && $_FILES['arquivo_csv']['error'] === UPLOAD_ERR_OK) {
            $file = fopen($_FILES['arquivo_csv']['tmp_name'], 'r');
            $primeiraLinha = fgets($file);
            $delimitador = strpos($primeiraLinha, ';') !== false ? ';' : ',';
            rewind($file);
            fgetcsv($file, 1000, $delimitador);

            while (($linha = fgetcsv($file, 1000, $delimitador)) !== FALSE) {
                if (empty(trim($linha[0]))) continue;
                $emergencia_raw = isset($linha[9]) ? trim($linha[9]) : '';
                $tel_emerg = ''; $contato_emerg = $emergencia_raw;
                if (preg_match('/^([0-9\s\-\(\)]+)(?:[-–]*\s*)(.*)$/u', $emergencia_raw, $matches)) {
                    $tel_emerg = trim(rtrim($matches[1], " -"));
                    $contato_emerg = trim(ltrim($matches[2], " -"));
                }
                $novoFunc = array(
                    'id' => uniqid(),
                    'nome' => isset($linha[0]) ? trim($linha[0]) : '',
                    'rg' => isset($linha[1]) ? trim($linha[1]) : '',
                    'cpf' => isset($linha[2]) ? trim($linha[2]) : '',
                    'email_pessoal' => '',
                    'tel_empresarial' => isset($linha[4]) ? trim($linha[4]) : '',
                    'tel_contato' => isset($linha[7]) ? trim($linha[7]) : '',
                    'tel_emergencia' => $tel_emerg,
                    'contato_emergencia' => $contato_emerg,
                    'tipo_tecnico' => '',
                    'arquivo_aso' => '',
                    'arquivo_nr' => '',
                    'arquivo_cnh' => '',
                    'foto_perfil' => ''
                );
                $funcionarios[] = $novoFunc;
            }
            fclose($file);
            file_put_contents($arquivoJson, json_encode($funcionarios));
        }
        header("Location: index.php");
        exit;
    }

    // Ação: Salvar/Editar Técnico
    if ($acao === 'salvar') {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : uniqid();
        $dados = array(
            'id' => $id,
            'nome' => $_POST['nome'],
            'rg' => $_POST['rg'],
            'cpf' => $_POST['cpf'],
            'email_pessoal' => isset($_POST['email_pessoal']) ? trim($_POST['email_pessoal']) : '',
            'tel_empresarial' => $_POST['tel_empresarial'],
            'tel_contato' => $_POST['tel_contato'],
            'tel_emergencia' => $_POST['tel_emergencia'],
            'contato_emergencia' => $_POST['contato_emergencia'],
            'tipo_tecnico' => isset($_POST['tipo_tecnico']) ? $_POST['tipo_tecnico'] : 'acesso',
            'arquivo_aso' => isset($_POST['arquivo_aso_atual']) ? $_POST['arquivo_aso_atual'] : '',
            'arquivo_nr' => isset($_POST['arquivo_nr_atual']) ? $_POST['arquivo_nr_atual'] : '',
            'arquivo_cnh' => isset($_POST['arquivo_cnh_atual']) ? $_POST['arquivo_cnh_atual'] : '',
            'foto_perfil' => isset($_POST['foto_perfil_atual']) ? $_POST['foto_perfil_atual'] : ''
        );

        $novaFoto = processarDocumento($_FILES['foto_perfil'], 'perfil', $id, $diretorioUploads);
        if ($novaFoto !== null) $dados['foto_perfil'] = $novaFoto;

        $novoAso = processarDocumento($_FILES['foto_aso'], 'aso', $id, $diretorioUploads);
        if ($novoAso !== null) $dados['arquivo_aso'] = $novoAso;

        $novaNr = processarDocumento($_FILES['foto_nr'], 'nr', $id, $diretorioUploads);
        if ($novaNr !== null) $dados['arquivo_nr'] = $novaNr;

        $novaCnh = processarDocumento($_FILES['foto_cnh'], 'cnh', $id, $diretorioUploads);
        if ($novaCnh !== null) $dados['arquivo_cnh'] = $novaCnh;

        $encontrou = false;
        foreach ($funcionarios as $key => $func) {
            if ($func['id'] === $id) {
                $funcionarios[$key] = $dados;
                $encontrou = true;
                break;
            }
        }
        if (!$encontrou) $funcionarios[] = $dados;

        file_put_contents($arquivoJson, json_encode($funcionarios));
        header("Location: index.php");
        exit;
    }

    // Ação: Excluir Técnico
    if ($acao === 'excluir') {
        $id = $_POST['id'];
        foreach ($funcionarios as $key => $func) {
            if ($func['id'] === $id) {
                if (!empty($func['arquivo_aso']) && file_exists($diretorioUploads . $func['arquivo_aso'])) @unlink($diretorioUploads . $func['arquivo_aso']);
                if (!empty($func['arquivo_nr']) && file_exists($diretorioUploads . $func['arquivo_nr'])) @unlink($diretorioUploads . $func['arquivo_nr']);
                if (!empty($func['arquivo_cnh']) && file_exists($diretorioUploads . $func['arquivo_cnh'])) @unlink($diretorioUploads . $func['arquivo_cnh']);
                if (!empty($func['foto_perfil']) && file_exists($diretorioUploads . $func['foto_perfil'])) @unlink($diretorioUploads . $func['foto_perfil']);
                unset($funcionarios[$key]);
                break;
            }
        }
        $funcionarios = array_values($funcionarios);
        file_put_contents($arquivoJson, json_encode($funcionarios));
        header("Location: index.php");
        exit;
    }

    // Ação: Salvar / Modificar Utilizador (Painel Admin)
    if ($acao === 'salvar_usuario') {
        $username_form = trim($_POST['novo_username']);
        $password_form = trim($_POST['nova_senha']);
        if (!empty($username_form) && !empty($password_form)) {
            $encontrouUsuario = false;
            foreach ($usuarios as $key => $u) {
                if ($u['username'] === $username_form) {
                    $usuarios[$key]['password'] = password_hash($password_form, PASSWORD_DEFAULT);
                    $encontrouUsuario = true; break;
                }
            }
            if (!$encontrouUsuario) {
                $usuarios[] = array('username' => $username_form, 'password' => password_hash($password_form, PASSWORD_DEFAULT));
            }
            file_put_contents($arquivoUsuariosJson, json_encode($usuarios));
        }
        header("Location: index.php");
        exit;
    }

    // Ação: Excluir Utilizador
    if ($acao === 'excluir_usuario') {
        $user_para_excluir = $_POST['username_excluir'];
        if ($user_para_excluir !== 'admin') {
            foreach ($usuarios as $key => $u) {
                if ($u['username'] === $user_para_excluir) { unset($usuarios[$key]); break; }
            }
            $usuarios = array_values($usuarios);
            file_put_contents($arquivoUsuariosJson, json_encode($usuarios));
        }
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Documentos - Técnicos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        // Carrega o tema imediatamente para evitar flash na tela principal
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <style>
        /* VARIÁVEIS DE CORES PARA OS TEMAS */
        :root {
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --card-bg: #1e1e1e;
            --table-bg: #252525;
            --border-color: #333;
            --th-bg: #1a1a1a;
            --tr-hover: #2c2c2c;
            --input-bg: #333;
            --input-border: #444;
            --text-muted: #aaa;
            --text-strong: #fff;
            --accent-orange: #ff9800;
            --btn-theme: #5c6bc0;
            --shadow-color: rgba(0,0,0,0.3);
        }

        :root.light-mode {
            --bg-color: #f0f2f5;
            --text-color: #333333;
            --card-bg: #ffffff;
            --table-bg: #ffffff;
            --border-color: #e0e0e0;
            --th-bg: #f8f9fa;
            --tr-hover: #f1f5f9;
            --input-bg: #ffffff;
            --input-border: #cccccc;
            --text-muted: #666666;
            --text-strong: #000000;
            --accent-orange: #d84315;
            --btn-theme: #3949ab;
            --shadow-color: rgba(0,0,0,0.1);
        }

        body { background-color: var(--bg-color); color: var(--text-color); font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; transition: background-color 0.3s, color 0.3s; }
        .container { max-width: 1000px; margin: 0 auto; background-color: var(--card-bg); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px var(--shadow-color); transition: background-color 0.3s; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }
        .user-info { font-size: 14px; color: var(--text-muted); }
        .user-info strong { color: var(--text-strong); margin-right: 15px;}
        
        h2, h3, h4 { color: var(--text-strong); }

        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: white; text-decoration: none; display: inline-block; transition: background-color 0.2s; }
        .btn-add { background-color: #2e7d32; }
        .btn-import { background-color: #0277bd; margin-right: 5px; }
        .btn-export { background-color: #00897b; margin-right: 5px; }
        .btn-user { background-color: #7b1fa2; margin-right: 5px; }
        .btn-theme-btn { background-color: var(--btn-theme); margin-right: 5px; }
        .btn-pwd { background-color: #f57c00; font-size: 12px; padding: 5px 10px;}
        .btn-logout { background-color: #616161; font-size: 12px; padding: 5px 10px; margin-left: 5px;}
        .btn-edit { background-color: #1976d2; padding: 6px 10px; }
        .btn-delete { background-color: #d32f2f; padding: 6px 10px; }
        .btn-doc { background-color: #2e7d32; padding: 4px 8px; font-size: 12px; border-radius: 12px; }
        .btn-none { background-color: var(--input-bg); padding: 4px 8px; font-size: 12px; border-radius: 12px; color: var(--text-muted); border: 1px solid var(--border-color); cursor: default; }
        
        table { width: 100%; border-collapse: collapse; background-color: var(--table-bg); border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        th { background-color: var(--th-bg); font-weight: 600; color: var(--text-strong); }
        tr:hover { background-color: var(--tr-hover); }
        
        /* Estilos do Bloco do Técnico (Foto + Textos) */
        .tech-profile { display: table; }
        .tech-avatar-cell { display: table-cell; vertical-align: middle; padding-right: 15px; }
        .tech-info-cell { display: table-cell; vertical-align: middle; cursor: pointer; transition: color 0.2s; }
        .tech-info-cell strong { color: var(--text-strong); }
        .tech-info-cell:hover strong { color: #4caf50; }
        
        .tech-photo { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; background-color: var(--input-bg); border: 2px solid var(--border-color); display: block; cursor: pointer; transition: transform 0.2s; }
        .tech-photo:hover { transform: scale(1.1); border-color: #1976d2; }
        .tech-no-photo { width: 45px; height: 45px; border-radius: 50%; background-color: var(--input-bg); border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 18px; }

        /* Modais */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background-color: var(--card-bg); color: var(--text-color); padding: 20px; border-radius: 8px; width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 4px 15px var(--shadow-color); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 14px; color: var(--text-color); }
        .form-group input, .form-group select, .filter-select { width: 100%; padding: 8px; background-color: var(--input-bg); border: 1px solid var(--input-border); color: var(--text-color); border-radius: 4px; box-sizing: border-box; }
        .form-row { display: flex; gap: 10px; }
        .form-row .form-group { flex: 1; }
        .modal-footer { margin-top: 20px; text-align: right; }
        .btn-cancel { background-color: var(--text-muted); margin-right: 10px; color: #fff; }
        .user-list-item { display: flex; justify-content: space-between; align-items: center; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px; margin-bottom: 5px; border-radius: 4px; }
        .filter-bar { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 15px; }
        .filter-bar label { color: var(--text-muted); font-size: 14px; }
        .filter-select { width: auto; min-width: 190px; }

        /* Visualizador de Imagem (Lightbox) */
        .photo-viewer { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); align-items: center; justify-content: center; z-index: 2000; cursor: pointer; }
        .photo-viewer img { max-width: 90%; max-height: 90%; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.8); cursor: default; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-bar">
        <div>
            <h2 style="margin:0 0 5px 0;">Documentação de Técnicos</h2>
            <div class="user-info">
                Utilizador: <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong>
                <button class="btn btn-pwd" onclick="openPwdModal()"><i class="fa fa-key"></i> Mudar Senha</button>
                <a href="index.php?action=logout" class="btn btn-logout"><i class="fa fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
        <div>
            <!-- Botão de Tema -->
            <button class="btn btn-theme-btn" onclick="toggleTheme()" title="Alternar Modo Claro / Escuro"><i class="fa fa-sun" id="themeIcon"></i></button>
            
            <?php if($_SESSION['usuario'] === 'admin'): ?>
                <button class="btn btn-user" onclick="openUserModal()" title="Gerir Utilizadores"><i class="fa fa-user-gear"></i></button>
            <?php endif; ?>
            <a href="index.php?action=exportar_excel" class="btn btn-export" title="Baixar a lista em formato compatível com Excel"><i class="fa fa-file-excel"></i> Exportar Excel</a>
            <button class="btn btn-import" onclick="openImportModal()"><i class="fa fa-file-csv"></i> Importar CSV</button>
            <button class="btn btn-add" onclick="openModal()"><i class="fa fa-plus"></i> Adicionar</button>
        </div>
    </div>

    <div class="filter-bar">
        <label for="filtro_tipo_tecnico">Filtrar por tipo</label>
        <select id="filtro_tipo_tecnico" class="filter-select" onchange="filtrarTecnicos()">
            <option value="todos" selected>Todos</option>
            <option value="acesso">Técnico de acesso</option>
            <option value="redes">Técnico de redes</option>
        </select>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>ASO</th>
                <th>NR</th>
                <th>CNH</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($funcionarios)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Nenhum técnico registado.</td></tr>
            <?php endif; ?>
            <?php foreach (array_reverse($funcionarios) as $func): ?>
            <tr data-tipo-tecnico="<?php echo isset($func['tipo_tecnico']) ? htmlspecialchars($func['tipo_tecnico']) : ''; ?>">
                <td>
                    <div class="tech-profile">
                        <div class="tech-avatar-cell">
                            <?php if (!empty($func['foto_perfil']) && file_exists($diretorioUploads . $func['foto_perfil'])): ?>
                                <?php $v = filemtime($diretorioUploads . $func['foto_perfil']); ?>
                                <!-- Clique na foto abre o Visualizador -->
                                <img src="uploads/<?php echo $func['foto_perfil']; ?>?v=<?php echo $v; ?>" class="tech-photo" alt="Foto" onclick="viewPhoto('uploads/<?php echo $func['foto_perfil']; ?>?v=<?php echo $v; ?>')">
                            <?php else: ?>
                                <div class="tech-no-photo"><i class="fa fa-user"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="tech-info-cell" onclick='viewInfo(<?php echo htmlspecialchars(json_encode($func), ENT_QUOTES, 'UTF-8'); ?>)' title="Clique para ver mais detalhes e copiar">
                            <strong><?php echo htmlspecialchars($func['nome']); ?></strong><br>
                            <span style="font-size: 12px; color: var(--text-muted);">RG: <?php echo htmlspecialchars($func['rg']); ?></span>
                        </div>
                    </div>
                </td>
                <td>
                    <?php if (!empty($func['arquivo_aso'])): ?>
                        <?php $v = @filemtime($diretorioUploads . $func['arquivo_aso']); ?>
                        <a href="uploads/<?php echo $func['arquivo_aso']; ?>?v=<?php echo $v; ?>" target="_blank" class="btn btn-doc"><i class="fa fa-check-circle"></i> Ver Doc</a>
                    <?php else: ?>
                        <span class="btn btn-none">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($func['arquivo_nr'])): ?>
                        <?php $v = @filemtime($diretorioUploads . $func['arquivo_nr']); ?>
                        <a href="uploads/<?php echo $func['arquivo_nr']; ?>?v=<?php echo $v; ?>" target="_blank" class="btn btn-doc"><i class="fa fa-check-circle"></i> Ver Doc</a>
                    <?php else: ?>
                        <span class="btn btn-none">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($func['arquivo_cnh'])): ?>
                        <?php $v = @filemtime($diretorioUploads . $func['arquivo_cnh']); ?>
                        <a href="uploads/<?php echo $func['arquivo_cnh']; ?>?v=<?php echo $v; ?>" target="_blank" class="btn btn-doc"><i class="fa fa-check-circle"></i> Ver Doc</a>
                    <?php else: ?>
                        <span class="btn btn-none">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-edit" onclick='editFunc(<?php echo htmlspecialchars(json_encode($func), ENT_QUOTES, 'UTF-8'); ?>)'><i class="fa fa-pencil"></i></button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Tem a certeza que deseja excluir este técnico?');">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?php echo $func['id']; ?>">
                        <button type="submit" class="btn btn-delete"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- CAIXA DE VISUALIZAÇÃO DE FOTO (LIGHTBOX) -->
<div class="photo-viewer" id="photoViewerModal" onclick="closePhotoView()">
    <img id="fullSizeImage" src="" alt="Foto Ampliada" onclick="event.stopPropagation();">
</div>

<!-- CAIXA DE VISUALIZAÇÃO DE DADOS (COPIAR DADOS) -->
<div class="modal" id="infoViewerModal" onclick="closeInfoView()">
    <div class="modal-content" onclick="event.stopPropagation();" style="width: 400px;">
        <h3 style="margin-top:0; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;"><i class="fa fa-id-card"></i> Dados do Técnico</h3>
        <div id="infoViewerContent" style="line-height: 1.6; font-size: 14px; color: var(--text-color);">
            <!-- Conteúdo inserido via JS -->
        </div>
        <div class="modal-footer" style="margin-top: 20px;">
            <button type="button" class="btn btn-cancel" onclick="closeInfoView()">Fechar</button>
            <button type="button" class="btn btn-import" onclick="copyInfo()"><i class="fa fa-copy"></i> Copiar Dados</button>
        </div>
    </div>
</div>

<!-- MODAL: MUDAR PALAVRA-PASSE (UTILIZADOR ATUAL) -->
<div class="modal" id="pwdModal">
    <div class="modal-content" style="width: 400px;">
        <h3 style="margin-top:0;">Mudar a Minha Senha</h3>
        <form method="POST">
            <input type="hidden" name="acao" value="mudar_minha_senha">
            <div class="form-group">
                <label>Palavra-passe Atual</label>
                <input type="password" name="senha_atual" required>
            </div>
            <div class="form-group">
                <label>Nova Palavra-passe</label>
                <input type="password" name="nova_senha" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closePwdModal()">Cancelar</button>
                <button type="submit" class="btn btn-pwd" style="font-size: 14px;">Atualizar Senha</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: IMPORTAR CSV -->
<div class="modal" id="importModal">
    <div class="modal-content" style="width: 400px;">
        <h3 style="margin-top:0;">Importar Planilha</h3>
        <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">
            1. No seu Excel ou Google Sheets, vá a <strong>Ficheiro > Guardar como</strong> (ou Fazer download).<br>
            2. Escolha o formato <strong>CSV (Separado por vírgulas)</strong>.<br>
            3. Envie o ficheiro abaixo (as colunas CNH e IMEIs serão ignoradas).
        </p>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="importar_csv">
            <div class="form-group">
                <input type="file" name="arquivo_csv" accept=".csv" required style="padding: 10px; background: var(--input-bg); border: 1px solid var(--border-color);">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeImportModal()">Cancelar</button>
                <button type="submit" class="btn btn-import">Importar Dados</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: FORMULÁRIO TÉCNICO -->
<div class="modal" id="formModal">
    <div class="modal-content">
        <h3 id="modalTitle" style="margin-top:0;">Adicionar Técnico</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="salvar">
            <input type="hidden" name="id" id="func_id">
            <input type="hidden" name="arquivo_aso_atual" id="arquivo_aso_atual">
            <input type="hidden" name="arquivo_nr_atual" id="arquivo_nr_atual">
            <input type="hidden" name="arquivo_cnh_atual" id="arquivo_cnh_atual">
            <input type="hidden" name="foto_perfil_atual" id="foto_perfil_atual">

            <div class="form-group">
                <label>Foto de Perfil <span style="font-size:11px; color:var(--text-muted);">(Imagem converte para WebP)</span></label>
                <input type="file" name="foto_perfil" accept="image/*">
                <small id="foto_status" style="color: #4caf50; display:block; margin-top:4px;"></small>
            </div>

            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" id="nome" required>
            </div>

            <div class="form-group">
                <label>E-mail Pessoal</label>
                <input type="email" name="email_pessoal" id="email_pessoal" placeholder="exemplo@email.com">
            </div>

            <div class="form-group">
                <label>Tipo de Técnico</label>
                <select name="tipo_tecnico" id="tipo_tecnico" required>
                    <option value="acesso">Técnico de acesso</option>
                    <option value="redes">Técnico de redes</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>RG</label>
                    <input type="text" name="rg" id="rg" required>
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="cpf" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Telefone Empresarial</label>
                    <input type="text" name="tel_empresarial" id="tel_empresarial">
                </div>
                <div class="form-group">
                    <label>Telefone de Contacto</label>
                    <input type="text" name="tel_contato" id="tel_contato" required>
                </div>
            </div>

            <div class="form-group" style="background: var(--input-bg); border: 1px solid var(--border-color); padding: 10px; border-radius: 4px;">
                <label style="color:var(--accent-orange); font-weight:bold;">Contacto de Emergência</label>
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="text" name="contato_emergencia" id="contato_emergencia" placeholder="Nome / Parentesco" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="text" name="tel_emergencia" id="tel_emergencia" placeholder="Telefone" required>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label>Foto/Documento ASO</label>
                <input type="file" name="foto_aso" accept="image/*,.pdf">
                <small id="aso_status" style="color: #4caf50; display:block; margin-top:4px;"></small>
            </div>

            <div class="form-group">
                <label>Foto/Documento NR</label>
                <input type="file" name="foto_nr" accept="image/*,.pdf">
                <small id="nr_status" style="color: #4caf50; display:block; margin-top:4px;"></small>
            </div>

            <div class="form-group">
                <label>Foto/Documento CNH</label>
                <input type="file" name="foto_cnh" accept="image/*,.pdf">
                <small id="cnh_status" style="color: #4caf50; display:block; margin-top:4px;"></small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn btn-add">Guardar Dados</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: GERENCIAMENTO DE USUÁRIOS (Apenas Admin vê o botão) -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <h3 style="margin-top:0;">Gerir Utilizadores</h3>
        <form method="POST" style="background:var(--th-bg); border: 1px solid var(--border-color); padding:15px; border-radius:6px; margin-bottom:20px;">
            <input type="hidden" name="acao" value="salvar_usuario">
            <div class="form-group">
                <label>Nome do Utilizador</label>
                <input type="text" name="novo_username" placeholder="Ex: admin" required>
            </div>
            <div class="form-group">
                <label>Nova Palavra-passe</label>
                <input type="password" name="nova_senha" required>
            </div>
            <button type="submit" class="btn btn-add" style="width:100%;">Guardar Utilizador</button>
        </form>

        <h4 style="margin:0 0 10px 0;">Utilizadores com Acesso</h4>
        <div style="max-height: 150px; overflow-y:auto;">
            <?php foreach($usuarios as $u): ?>
                <div class="user-list-item">
                    <span><i class="fa fa-user" style="color:var(--text-muted); margin-right:8px;"></i> <?php echo htmlspecialchars($u['username']); ?></span>
                    <?php if($u['username'] === 'admin'): ?>
                        <span style="font-size:11px; color:#2e7d32; font-weight:bold; padding-right:10px;">Master</span>
                    <?php else: ?>
                        <form method="POST" onsubmit="return confirm('Excluir acesso deste utilizador?');" style="margin:0;">
                            <input type="hidden" name="acao" value="excluir_usuario">
                            <input type="hidden" name="username_excluir" value="<?php echo htmlspecialchars($u['username']); ?>">
                            <button type="submit" class="btn btn-delete" style="padding:4px 8px; font-size:11px;"><i class="fa fa-trash"></i></button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-cancel" onclick="closeUserModal()">Fechar</button>
        </div>
    </div>
</div>

<script>
    // Inicialização do Icone do Tema
    window.addEventListener('DOMContentLoaded', () => {
        const icon = document.getElementById('themeIcon');
        if (localStorage.getItem('theme') === 'light' && icon) {
            icon.className = 'fa fa-moon';
        }

        const filtroTipoTecnico = document.getElementById('filtro_tipo_tecnico');
        if (filtroTipoTecnico) {
            filtroTipoTecnico.value = 'todos';
            filtroTipoTecnico.addEventListener('change', filtrarTecnicos);
            filtrarTecnicos();
        }
    });

    // Função de Alternar Tema (Modo Claro/Escuro)
    function toggleTheme() {
        const root = document.documentElement;
        root.classList.toggle('light-mode');
        const isLight = root.classList.contains('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        
        const icon = document.getElementById('themeIcon');
        if (icon) {
            icon.className = isLight ? 'fa fa-moon' : 'fa fa-sun';
        }
    }

    const modal = document.getElementById('formModal');
    const userModal = document.getElementById('userModal');
    const importModal = document.getElementById('importModal');
    const pwdModal = document.getElementById('pwdModal');
    const photoViewerModal = document.getElementById('photoViewerModal');
    const fullSizeImage = document.getElementById('fullSizeImage');
    const infoViewerModal = document.getElementById('infoViewerModal');
    const infoViewerContent = document.getElementById('infoViewerContent');
    let currentInfoText = '';

    // Funções de Visualização de Dados (Novo)
    function viewInfo(func) {
        const tipoTecnico = func.tipo_tecnico === 'redes' ? 'Técnico de redes' : 'Técnico de acesso';
        currentInfoText = `Nome: ${func.nome}\nTipo: ${tipoTecnico}\nRG: ${func.rg}\nCPF: ${func.cpf}\nE-mail Pessoal: ${func.email_pessoal || 'N/A'}\nTel Empresarial: ${func.tel_empresarial || 'N/A'}\nTel Contacto: ${func.tel_contato}\nContacto de Emergência: ${func.contato_emergencia} (${func.tel_emergencia})`;

        infoViewerContent.innerHTML = `
            <p style="margin: 5px 0;"><strong>Nome:</strong> <span style="color:var(--text-strong);">${func.nome}</span></p>
            <p style="margin: 5px 0;"><strong>Tipo:</strong> <span style="color:var(--text-strong);">${tipoTecnico}</span></p>
            <p style="margin: 5px 0;"><strong>RG:</strong> <span style="color:var(--text-strong);">${func.rg}</span></p>
            <p style="margin: 5px 0;"><strong>CPF:</strong> <span style="color:var(--text-strong);">${func.cpf}</span></p>
            <p style="margin: 5px 0;"><strong>E-mail Pessoal:</strong> <span style="color:var(--text-strong);">${func.email_pessoal || '-'}</span></p>
            <p style="margin: 5px 0;"><strong>Tel Empresarial:</strong> <span style="color:var(--text-strong);">${func.tel_empresarial || '-'}</span></p>
            <p style="margin: 5px 0;"><strong>Tel Contacto:</strong> <span style="color:var(--text-strong);">${func.tel_contato}</span></p>
            <div style="margin-top:15px; padding-top:10px; border-top: 1px solid var(--border-color);">
                <strong style="color:var(--accent-orange);">Contacto de Emergência:</strong><br>
                <span style="color:var(--text-strong);">${func.contato_emergencia} - ${func.tel_emergencia}</span>
            </div>
        `;
        infoViewerModal.style.display = 'flex';
    }

    function closeInfoView() {
        infoViewerModal.style.display = 'none';
    }

    function copyInfo() {
        const textArea = document.createElement("textarea");
        textArea.value = currentInfoText;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Dados copiados para a área de transferência!');
        } catch (err) {
            alert('Erro ao copiar dados.');
        }
        document.body.removeChild(textArea);
    }

    // Funções de Visualização de Foto
    function viewPhoto(url) {
        fullSizeImage.src = url;
        photoViewerModal.style.display = 'flex';
    }
    function closePhotoView() {
        photoViewerModal.style.display = 'none';
        fullSizeImage.src = '';
    }

    // Funções de Modais
    function openModal() {
        document.getElementById('modalTitle').innerText = 'Adicionar Técnico';
        document.getElementById('func_id').value = '';
        document.getElementById('tipo_tecnico').value = 'acesso';
        document.getElementById('arquivo_aso_atual').value = '';
        document.getElementById('arquivo_nr_atual').value = '';
        document.getElementById('arquivo_cnh_atual').value = '';
        document.getElementById('foto_perfil_atual').value = '';
        document.getElementById('aso_status').innerText = '';
        document.getElementById('nr_status').innerText = '';
        document.getElementById('cnh_status').innerText = '';
        document.getElementById('foto_status').innerText = '';
        
        const inputs = modal.querySelectorAll('input[type="text"], input[type="email"]');
        inputs.forEach(input => input.value = '');
        modal.style.display = 'flex';
    }

    function closeModal() { modal.style.display = 'none'; }
    function openUserModal() { userModal.style.display = 'flex'; }
    function closeUserModal() { userModal.style.display = 'none'; }
    function openImportModal() { importModal.style.display = 'flex'; }
    function closeImportModal() { importModal.style.display = 'none'; }
    function openPwdModal() { pwdModal.style.display = 'flex'; }
    function closePwdModal() { pwdModal.style.display = 'none'; }

    // Editar Funcionario
    function editFunc(func) {
        document.getElementById('modalTitle').innerText = 'Editar Técnico';
        document.getElementById('func_id').value = func.id;
        document.getElementById('nome').value = func.nome;
        document.getElementById('rg').value = func.rg;
        document.getElementById('cpf').value = func.cpf;
        document.getElementById('email_pessoal').value = func.email_pessoal || '';
        document.getElementById('tipo_tecnico').value = func.tipo_tecnico || 'acesso';
        document.getElementById('tel_empresarial').value = func.tel_empresarial;
        document.getElementById('tel_contato').value = func.tel_contato;
        document.getElementById('tel_emergencia').value = func.tel_emergencia;
        document.getElementById('contato_emergencia').value = func.contato_emergencia;
        
        document.getElementById('arquivo_aso_atual').value = func.arquivo_aso || '';
        document.getElementById('arquivo_nr_atual').value = func.arquivo_nr || '';
        document.getElementById('arquivo_cnh_atual').value = func.arquivo_cnh || '';
        document.getElementById('foto_perfil_atual').value = func.foto_perfil || '';

        document.getElementById('foto_status').innerText = func.foto_perfil ? 'Foto atual: ' + func.foto_perfil : 'Sem foto de perfil.';
        document.getElementById('aso_status').innerText = func.arquivo_aso ? 'Documento atual: ' + func.arquivo_aso : 'Nenhum ASO salvo.';
        document.getElementById('nr_status').innerText = func.arquivo_nr ? 'Documento atual: ' + func.arquivo_nr : 'Nenhuma NR salva.';
        document.getElementById('cnh_status').innerText = func.arquivo_cnh ? 'Documento atual: ' + func.arquivo_cnh : 'Nenhuma CNH salva.';

        modal.style.display = 'flex';
    }

    function filtrarTecnicos() {
        const filtro = document.getElementById('filtro_tipo_tecnico').value;
        const linhas = document.querySelectorAll('tbody tr[data-tipo-tecnico]');

        linhas.forEach((linha) => {
            const tipo = linha.getAttribute('data-tipo-tecnico') || 'acesso';
            linha.style.display = filtro === 'todos' || tipo === filtro ? '' : 'none';
        });
    }
</script>

</body>
</html>
