<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - Frente de Loja</title>
    <style>
        :root {
            --primaria: #3A37E0;
            --secundaria: #f5f5f5;
            --texto: #333;
            --verde: #c8f7c5;
            --verde-claro: #d1fad1;
            --azul-claro: #eef1fb;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: white;
            color: var(--texto);
        }
        header {
            padding: 10px;
            background-color: var(--primaria);
            color: white;
            text-align: center;
        }
        main {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .left, .right {
            background: var(--secundaria);
            padding: 15px;
            border-radius: 8px;
            flex: 1;
            min-width: 320px;
        }
        h2 {
            margin-top: 0;
        }
        label {
            display: block;
            margin: 5px 0 2px;
        }
        input, select, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: var(--azul-claro);
        }
        .total {
            background: var(--verde);
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }
        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .buttons button {
            flex: 1;
            background-color: var(--primaria);
            color: white;
            border: none;
            cursor: pointer;
        }
        .buttons button:hover {
            background-color: #2a27c0;
        }
        .resumo {
            display: flex;
            justify-content: space-between;
            background: var(--verde-claro);
            padding: 8px;
            border-radius: 5px;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            main {
                flex-direction: column;
            }
            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>PDV - Pedido de Venda</h1>
</header>

<main>
    <!-- Lado Esquerdo - Itens -->
    <div class="left">
        <h2>Itens da Venda</h2>
        <label>Código</label>
        <input type="text" id="codigo" placeholder="Código">

        <label>Quantidade</label>
        <input type="number" id="quantidade" value="1" step="0.001">

        <label>Valor Unitário</label>
        <input type="number" id="valorUnit" step="0.01">

        <button onclick="adicionarItem()">Inserir</button>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qtde</th>
                    <th>Valor Unit.</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaItens"></tbody>
        </table>

        <div class="resumo">
            <div>Itens: <span id="qtdItens">0</span></div>
            <div>Subtotal: R$ <span id="subtotal">0.00</span></div>
        </div>

        <div class="total">
            Total: R$ <span id="total">0.00</span>
        </div>
    </div>

    <!-- Lado Direito - Pagamento -->
    <div class="right">
        <h2>Pagamento</h2>

        <label>Código Cliente</label>
        <input type="text" placeholder="Cód Cliente">

        <label>Documento</label>
        <input type="text" placeholder="CPF/CNPJ">

        <label>Vendedor</label>
        <select>
            <option>Aguiar Junior</option>
        </select>

        <label>Observação</label>
        <input type="text" placeholder="Observações">

        <label>Forma de Pagamento</label>
        <select>
            <option>Dinheiro</option>
            <option>PIX</option>
            <option>Cartão</option>
        </select>

        <label>Valor Pago</label>
        <input type="number" step="0.01">

        <div class="total">
            Troco: R$ 0,00
        </div>

        <div class="buttons">
            <button>Finalizar (F1)</button>
            <button>PV (F2)</button>
            <button>Consultar Vendas (F8)</button>
            <button>Cancelar Venda (F9)</button>
            <button>Cancelar Item (F7)</button>
            <button>Pedidos Finalizados</button>
            <button>Trocar Tipo de Envio</button>
            <button>Tela Cheia (F11)</button>
            <button>Sair (ESC)</button>
        </div>
    </div>
</main>

<script>
    let itens = [];
    function adicionarItem() {
        const codigo = document.getElementById('codigo').value;
        const quantidade = parseFloat(document.getElementById('quantidade').value);
        const valorUnit = parseFloat(document.getElementById('valorUnit').value);
        if (!codigo || !quantidade || !valorUnit) {
            alert('Preencha todos os campos do item');
            return;
        }
        const total = quantidade * valorUnit;
        itens.push({codigo, quantidade, valorUnit, total});
        renderizarItens();
        limparCampos();
    }

    function removerItem(index) {
        itens.splice(index, 1);
        renderizarItens();
    }

    function renderizarItens() {
        const tabela = document.getElementById('tabelaItens');
        tabela.innerHTML = '';
        let subtotal = 0;
        itens.forEach((item, index) => {
            subtotal += item.total;
            tabela.innerHTML += `
                <tr>
                    <td>${item.codigo}</td>
                    <td>${item.quantidade.toFixed(3)}</td>
                    <td>R$ ${item.valorUnit.toFixed(2)}</td>
                    <td>R$ ${item.total.toFixed(2)}</td>
                    <td><button onclick="removerItem(${index})">Remover</button></td>
                </tr>
            `;
        });
        document.getElementById('qtdItens').innerText = itens.length;
        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('total').innerText = subtotal.toFixed(2);
    }

    function limparCampos() {
        document.getElementById('codigo').value = '';
        document.getElementById('quantidade').value = 1;
        document.getElementById('valorUnit').value = '';
    }
</script>

</body>
</html>
