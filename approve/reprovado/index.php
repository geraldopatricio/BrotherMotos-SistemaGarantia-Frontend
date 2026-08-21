<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Status da Garantia</title>

    <style>
    :root {
        --verde-primario: #2ecc71;
        --verde-claro: #a9dfbf;
        --fundo: #f4fdf7;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: var(--fundo);
        font-family: Arial, Helvetica, sans-serif;
    }

    .container-status {
        padding: 40px;
        border: 3px solid var(--verde-claro);
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .container-status:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.12);
    }

    .imagem-status {
        max-width: 100%;
        width: 420px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    @media (max-width: 480px) {
        .container-status {
            padding: 20px;
        }

        .imagem-status {
            width: 100%;
        }
    }
    </style>
</head>

<body>

    <div class="container-status">
        <img src="assets/logo.png" alt="Status da Garantia" class="imagem-status" />
    </div>

</body>

</html>