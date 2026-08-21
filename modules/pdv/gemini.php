<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - Frente de Loja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6A5ACD; /* Aproximado do roxo */
            --secondary-color: #4A4A4A;
            --light-bg: #F0F0F7;
            --light-gray: #EAEAEA;
            --white: #FFFFFF;
            --green-total: #C8E6C9; /* Verde claro para totais */
            --text-color: #333;
            --border-color: #DDD;
            --blue-button: #3F51B5;
            --dark-blue-button: #303F9F;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--white);
            color: var(--text-color);
            font-size: 14px;
            line-height: 1.5;
            padding: 10px;
        }

        .pdv-container {
            display: flex;
            flex-wrap: wrap; /* Permite que os painéis quebrem em telas menores */
            gap: 15px;
            max-width: 1500px; /* Max width do container geral */
            margin: 0 auto;
        }

        .left-pane, .right-pane {
            padding: 15px;
            background-color: var(--white);
            border-radius: 8px;
            /* box-shadow: 0 2px 5px rgba(0,0,0,0.1); */
        }

        .left-pane {
            flex: 2; /* Ocupa mais espaço */
            min-width: 300px; /* Largura mínima para evitar esmagamento */
        }

        .right-pane {
            flex: 3; /* Ocupa mais espaço */
            min-width: 400px;
        }
        
        .section-title {
            font-size: 1.2em;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 8px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 0.9em;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 0.95em;
            background-color: var(--light-bg);
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(106, 90, 205, 0.2);
        }

        .input-group {
            display: flex;
            align-items: center;
        }
        .input-group input {
            flex-grow: 1;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .input-group .btn, .input-group .icon-btn {
            padding: 8px 10px;
            border: 1px solid var(--border-color);
            background-color: var(--light-gray);
            cursor: pointer;
            border-left: none;
            height: 37px; /* Ajustar altura para alinhar com input */
        }
        .input-group .icon-btn {
             border-top-right-radius: 4px;
             border-bottom-right-radius: 4px;
        }
         .input-group .icon-btn:first-of-type {
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
            border-top-right-radius: 0px;
            border-bottom-right-radius: 0px;
        }
         .input-group .icon-btn:last-of-type {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }


        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95em;
            transition: background-color 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }
        .btn-primary:hover {
            background-color: #5a4ab0;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--white);
        }
        .btn-secondary:hover {
            background-color: #3a3a3a;
        }

        .btn-light {
            background-color: var(--light-gray);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        .btn-light:hover {
            background-color: #dcdcdc;
        }


        .item-entry-form {
            background-color: var(--light-bg);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .item-entry-form .form-row {
            display: flex;
            gap: 10px;
            align-items: flex-end; /* Alinha botão com base dos inputs */
        }
        .item-entry-form .form-row .form-group {
            flex: 1;
        }
        .item-entry-form .form-group.codigo { flex: 0.5; }
        .item-entry-form .form-group.quantidade { flex: 0.5; }
        .item-entry-form .form-group.valor-unit { flex: 0.7; }
        .item-entry-form .form-group.valor-total { flex: 0.7; }
        .item-entry-form .btn-inserir {
            height: 37px; /* Alinhar altura com inputs */
            flex-shrink: 0; /* Não encolher */
        }

        .items-table-container {
            max-height: 250px; /* Altura máxima para a lista de itens */
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th, .items-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .items-table th {
            background-color: var(--light-bg);
            font-weight: 600;
            position: sticky;
            top: 0; /* Para cabeçalho fixo ao rolar */
        }
        .items-table td.actions { text-align: center; }
        .items-table .action-icon { cursor: pointer; color: #e53935; }
        .items-table .action-icon:hover { color: #c62828; }

        .payment-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .payment-info .form-group {
            flex: 1;
            min-width: 150px; /* Para quebrar bem */
        }
        .radio-group {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .radio-group input[type="radio"] {
            margin-right: 5px;
        }
        .checkbox-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 5px;
        }

        .payment-methods-list {
            min-height: 50px;
            background-color: var(--light-bg);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-style: italic;
            color: #777;
        }
        .payment-methods-list div {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-style: normal;
            color: var(--text-color);
        }

        .summary-section {
            margin-top: 15px;
            background-color: var(--light-bg);
            padding: 10px;
            border-radius: 4px;
        }
        .summary-section .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 1em;
        }
        .summary-section .summary-item span:first-child { font-weight: 500; }
        .summary-section .total-value, .troco-value {
            font-size: 1.4em;
            font-weight: bold;
            color: var(--primary-color);
            text-align: right;
            padding: 8px;
            border-radius: 4px;
        }
        .troco-value {
            background-color: var(--green-total);
            color: var(--text-color); /* Cor do texto no troco */
        }


        .sales-summary-container {
            background-color: var(--white);
            padding: 15px;
            border-radius: 8px;
            /* box-shadow: 0 2px 5px rgba(0,0,0,0.1); */
            margin-top: 15px;
            width: 100%; /* Ocupa toda a largura abaixo dos painéis */
        }
        .sales-summary-content {
            display: flex;
            justify-content: space-around; /* Espaçar igualmente */
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .summary-box {
            background-color: var(--light-bg);
            padding: 10px 15px;
            border-radius: 6px;
            text-align: center;
            min-width: 120px; /* Largura mínima para cada caixa */
            flex: 1;
        }
        .summary-box p {
            font-size: 0.9em;
            color: var(--secondary-color);
        }
        .summary-box .value {
            font-size: 1.3em;
            font-weight: bold;
            color: var(--text-color);
        }
        .total-banner {
            background-color: var(--green-total);
            padding: 15px;
            text-align: center;
            border-radius: 6px;
        }
        .total-banner p {
            font-size: 1.1em;
            color: var(--secondary-color);
        }
        .total-banner .value {
            font-size: 1.8em;
            font-weight: bold;
            color: var(--text-color); /* Cor do texto no total */
        }

        .action-buttons-container {
            margin-top: 15px;
            padding: 10px 0; /* Sem padding lateral para botões ocuparem tudo */
            width: 100%; /* Ocupa toda a largura abaixo dos painéis */
        }
        .action-buttons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); /* Responsivo */
            gap: 10px;
        }
        .action-buttons-grid .btn {
            width: 100%;
            padding: 12px 10px; /* Aumentar padding */
            font-size: 0.9em;
        }
        .btn-finalize, .btn-pv, .btn-consultar, .btn-cancelar-venda, .btn-cancelar-item {
            background-color: var(--blue-button);
            color: white;
        }
        .btn-finalize:hover, .btn-pv:hover, .btn-consultar:hover, .btn-cancelar-venda:hover, .btn-cancelar-item:hover {
            background-color: var(--dark-blue-button);
        }
        .btn-financeiro, .btn-pedidos, .btn-trocar-envio, .btn-tela-cheia {
            background-color: var(--secondary-color);
            color: white;
        }
         .btn-financeiro:hover, .btn-pedidos:hover, .btn-trocar-envio:hover, .btn-tela-cheia:hover {
            background-color: #3a3a3a;
        }
        .btn-sair {
            background-color: var(--light-gray);
            color: var(--text-color);
        }
        .btn-sair:hover {
            background-color: #dcdcdc;
        }

        /* Ajustes para o form-group com botão de ícone na direita */
        .form-group-icon-right {
            position: relative;
        }
        .form-group-icon-right input {
            padding-right: 35px; /* Espaço para o ícone */
        }
        .form-group-icon-right .icon-inside {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            cursor: pointer;
        }

        /* Specific layout for fields in right pane */
        .client-doc-area, .seller-delivery-area {
            display: flex;
            gap: 10px;
            flex-wrap: wrap; /* Permitir quebra */
        }
        .client-doc-area .form-group, .seller-delivery-area .form-group {
            flex: 1;
        }
        .client-doc-area .form-group.cod-cliente { flex-basis: 100px; flex-grow: 0; }
        .client-doc-area .form-group.cliente { flex-basis: 200px; flex-grow: 1; }
        .client-doc-area .form-group.tipo-doc { flex-basis: 180px; flex-grow: 0; }
        .client-doc-area .form-group.documento { flex-basis: 150px; flex-grow: 1;}

        .seller-delivery-area .form-group.vendedor { flex-basis: 200px; flex-grow: 1; }
        .seller-delivery-area .form-group.entrega-obs { 
            flex-basis: 100%; /* Ocupar linha toda */
            display: flex;
            gap: 20px;
            align-items: center; /* Alinhar checkboxes */
            margin-top: 10px; /* Espaçamento para checkboxes */
        }

        .payment-form-area {
            display: flex;
            gap: 10px;
            align-items: flex-end; /* Alinhar botão */
        }
        .payment-form-area .form-group {
            flex: 1;
        }
        .payment-form-area .btn {
            height: 37px; /* Alinhar altura */
            flex-shrink: 0;
        }
        
        .discount-surcharge-area {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin-top: 10px;
        }
        .discount-surcharge-area .form-group {
            flex: 1;
        }
        .discount-surcharge-area .form-group.tipo-desconto {flex-basis: 150px; flex-grow: 0;}
        .discount-surcharge-area .form-group.valor-desconto {flex-basis: 120px; flex-grow: 0;}
        .discount-surcharge-area .btn {
            height: 37px;
            flex-shrink: 0;
        }


        /* Responsividade */
        @media (max-width: 1200px) {
            .left-pane { flex: 1 1 40%; }
            .right-pane { flex: 1 1 55%; }
        }

        @media (max-width: 992px) {
            .item-entry-form .form-row {
                flex-direction: column;
                align-items: stretch; /* Inputs ocupam largura total */
            }
            .item-entry-form .btn-inserir {
                width: 100%;
                margin-top: 10px;
            }
        }

        @media (max-width: 768px) {
            .pdv-container {
                flex-direction: column; /* Empilhar painéis */
            }
            .left-pane, .right-pane {
                flex-basis: auto; /* Resetar flex-basis */
                width: 100%;
            }
            .sales-summary-content .summary-box {
                min-width: 100px; /* Caixas menores */
            }
            .client-doc-area .form-group,
            .seller-delivery-area .form-group {
                 flex-basis: 100% !important; /* Campos ocupam 100% */
            }
            .payment-form-area, .discount-surcharge-area {
                flex-direction: column;
                align-items: stretch;
            }
            .payment-form-area .btn, .discount-surcharge-area .btn {
                width: 100%;
                margin-top: 10px;
            }
             .seller-delivery-area .form-group.entrega-obs {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <div class="pdv-container">
        <!-- Left Pane: Item Entry and List -->
        <div class="left-pane">
            <div class="item-entry-form">
                <div class="form-group">
                    <label for="item-search">CH PLANA 2,00MM SAC 300 6000MM X 1200MM - Código: 1564 - Preço: 7,20 - Estoque: 0,0000</label>
                    <div class="input-group">
                        <input type="text" id="item-search-display" value="1564 - CH PLANA..." disabled style="background-color: #e0e0e0;">
                        <button class="icon-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group codigo">
                        <label for="item-codigo">Código</label>
                        <input type="text" id="item-codigo" value="1564">
                    </div>
                    <div class="form-group quantidade">
                        <label for="item-quantidade">Quantidade</label>
                        <input type="number" id="item-quantidade" value="1.0000" step="0.0001">
                    </div>
                    <div class="form-group valor-unit">
                        <label for="item-valor-unit">Valor Unit.</label>
                        <input type="text" id="item-valor-unit" value="7,2000">
                    </div>
                    <div class="form-group valor-total">
                        <label for="item-valor-total-display">Valor total</label>
                        <input type="text" id="item-valor-total-display" value="R$7,20" readonly style="background-color: #e0e0e0;">
                    </div>
                    <button class="btn btn-primary btn-inserir" id="btn-add-item">Inserir</button>
                </div>
            </div>

            <div class="items-table-container">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qtde</th>
                            <th>Valor unit.</th>
                            <th>Desconto</th>
                            <th>Valor total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="items-list-body">
                        <!-- Itens serão adicionados aqui via JS -->
                        <tr>
                            <td>1-Cod: <strong>1060</strong> - TELHA SANDUICHE MT</td>
                            <td>1,0000</td>
                            <td>149,380</td>
                            <td>0,00</td>
                            <td><strong>149,38</strong></td>
                            <td class="actions"><i class="fas fa-ellipsis-h action-icon"></i></td>
                        </tr>
                         <tr>
                            <td>2-Cod: <strong>1150</strong> - CHAPA 12,50 X1200 X6000MM KG</td>
                            <td>1,0000</td>
                            <td>12,500</td>
                            <td>0,00</td>
                            <td><strong>12,50</strong></td>
                            <td class="actions"><i class="fas fa-ellipsis-h action-icon"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Pane: Payment Details -->
        <div class="right-pane">
            <h2 class="section-title"><i class="fas fa-credit-card"></i> Pagamento</h2>
            
            <div class="client-doc-area">
                <div class="form-group cod-cliente">
                    <label for="cliente-codigo">Código</label>
                    <input type="text" id="cliente-codigo" placeholder="Cód">
                </div>
                <div class="form-group cliente">
                    <label for="cliente-nome">Cliente</label>
                    <div class="input-group">
                        <input type="text" id="cliente-nome" placeholder="Cliente">
                        <button class="icon-btn"><i class="fas fa-search"></i></button>
                        <button class="icon-btn"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="form-group tipo-doc">
                    <label>Tipo de documento</label>
                    <div class="radio-group">
                        <label><input type="radio" name="tipo-documento" value="cpf" checked> CPF</label>
                        <label><input type="radio" name="tipo-documento" value="cnpj"> CNPJ</label>
                    </div>
                </div>
                <div class="form-group documento">
                    <label for="documento-numero">Documento</label>
                    <input type="text" id="documento-numero" value="000.000.000-00">
                </div>
            </div>

            <div class="seller-delivery-area">
                 <div class="form-group vendedor">
                    <label for="vendedor">Vendedor</label>
                    <div class="form-group-icon-right">
                        <input type="text" id="vendedor" value="Aguiar Junior">
                        <i class="fas fa-chevron-right icon-inside"></i>
                    </div>
                </div>
                <div class="form-group entrega-obs">
                    <div class="checkbox-group">
                        <label><input type="checkbox" id="entrega-endereco"> Entregar no Endereço</label>
                    </div>
                     <div class="checkbox-group">
                        <label><input type="checkbox" id="inserir-observacao" checked> Inserir Observação</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="observacao">Observação</label>
                <input type="text" id="observacao">
            </div>

            <div class="payment-form-area">
                <div class="form-group">
                    <label for="forma-pagamento">Forma de Pagamento</label>
                     <div class="form-group-icon-right">
                        <input type="text" id="forma-pagamento" value="DINHEIRO">
                        <i class="fas fa-chevron-right icon-inside"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="valor-pago">Valor Pago</label>
                    <input type="text" id="valor-pago" value="0,00">
                </div>
                <button class="btn btn-primary" id="btn-add-payment">Adicionar</button>
            </div>

            <label style="font-weight:500; font-size:0.9em; display:block; margin-top:15px; margin-bottom:5px;">Pagamentos Adicionados</label>
            <div class="payment-methods-list" id="payment-methods-list">
                Nenhum pagamento informado
            </div>

            <div class="form-group">
                <label>Ajuste de valor</label>
                 <div class="radio-group">
                    <label><input type="radio" name="ajuste-valor" value="desconto" checked> Desconto</label>
                    <label><input type="radio" name="ajuste-valor" value="acrescimo"> Acréscimo</label>
                </div>
            </div>

            <div class="discount-surcharge-area">
                 <div class="form-group tipo-desconto">
                    <label for="tipo-desconto">Tipo de desconto</label>
                     <div class="form-group-icon-right">
                        <input type="text" id="tipo-desconto" value="Valor R$">
                        <i class="fas fa-chevron-right icon-inside"></i>
                    </div>
                </div>
                <div class="form-group valor-desconto">
                    <label for="valor-desconto-input">Valor do desconto</label>
                    <input type="text" id="valor-desconto-input" value="R$ 0,00">
                </div>
                <button class="btn btn-light" id="btn-apply-discount"><i class="fas fa-plus"></i></button>
            </div>

            <div class="summary-section">
                 <h4 style="font-size: 1em; margin-bottom: 5px;">Resumo</h4>
                <div class="summary-item">
                    <span>Pago</span>
                    <span id="summary-pago">R$ 0,00</span>
                </div>
                <div class="summary-item">
                    <span>Restante</span>
                    <span id="summary-restante">R$ 0,00</span>
                </div>
                <div class="troco-value">
                    Troco <span id="summary-troco">R$ 0,00</span>
                </div>
            </div>
        </div>

        <!-- Sales Summary (Full Width) -->
        <div class="sales-summary-container">
            <h3 class="section-title" style="justify-content: center; margin-bottom: 15px;">Resumo desta venda</h3>
            <div class="sales-summary-content">
                <div class="summary-box">
                    <p>Itens</p>
                    <p class="value" id="summary-total-items">2</p>
                </div>
                <div class="summary-box">
                    <p>SubTotal</p>
                    <p class="value" id="summary-subtotal">R$ 161,88</p>
                </div>
                <div class="summary-box">
                    <p>Desconto</p>
                    <p class="value" id="summary-desconto-geral">0,00</p>
                </div>
            </div>
            <div class="total-banner">
                <p>Total</p>
                <p class="value" id="summary-total-geral">R$ 161,88</p>
            </div>
        </div>

        <!-- Action Buttons (Full Width) -->
        <div class="action-buttons-container">
            <div class="action-buttons-grid">
                <button class="btn btn-finalize">Finalizar (F1)</button>
                <button class="btn btn-pv">PV (F2)</button>
                <button class="btn btn-consultar">Consultar Vendas(F8)</button>
                <button class="btn btn-cancelar-venda">Cancelar Venda(F9)</button>
                <button class="btn btn-cancelar-item">Cancelar Item (F7)</button>
                
                <button class="btn btn-financeiro">Financeiro</button>
                <button class="btn btn-pedidos">Pedidos Finalizados</button>
                <button class="btn btn-trocar-envio">Trocar Tipo de Envio</button>
                <button class="btn btn-tela-cheia">Tela Cheia (F11)</button>
                <button class="btn btn-sair">Sair(ESC)</button>
            </div>
        </div>

    </div>

    <script>
        // Mock product data (for item entry simulation)
        const products = {
            "1564": { name: "CH PLANA 2,00MM SAC 300...", price: 7.20, stock: 0 },
            "1060": { name: "TELHA SANDUICHE MT", price: 149.38, stock: 10 },
            "1150": { name: "CHAPA 12,50 X1200 X6000MM KG", price: 12.50, stock: 50 }
        };

        let saleItems = [ // Initial items from the image
            { id: "1060", name: "TELHA SANDUICHE MT", quantity: 1.0000, unitPrice: 149.380, discount: 0.00, totalPrice: 149.38 },
            { id: "1150", name: "CHAPA 12,50 X1200 X6000MM KG", quantity: 1.0000, unitPrice: 12.500, discount: 0.00, totalPrice: 12.50 }
        ];
        let salePayments = [];
        let saleDiscount = 0; // Overall sale discount/surcharge

        // DOM Elements
        const itemCodigoInput = document.getElementById('item-codigo');
        const itemQuantidadeInput = document.getElementById('item-quantidade');
        const itemValorUnitInput = document.getElementById('item-valor-unit');
        const itemValorTotalDisplay = document.getElementById('item-valor-total-display');
        const itemSearchDisplay = document.getElementById('item-search-display');
        const btnAddItem = document.getElementById('btn-add-item');
        const itemsListBody = document.getElementById('items-list-body');

        const summaryTotalItems = document.getElementById('summary-total-items');
        const summarySubtotal = document.getElementById('summary-subtotal');
        const summaryDescontoGeral = document.getElementById('summary-desconto-geral');
        const summaryTotalGeral = document.getElementById('summary-total-geral');

        const valorPagoInput = document.getElementById('valor-pago');
        const btnAddPayment = document.getElementById('btn-add-payment');
        const paymentMethodsList = document.getElementById('payment-methods-list');
        const formaPagamentoInput = document.getElementById('forma-pagamento');

        const summaryPago = document.getElementById('summary-pago');
        const summaryRestante = document.getElementById('summary-restante');
        const summaryTroco = document.getElementById('summary-troco');
        
        const valorDescontoInput = document.getElementById('valor-desconto-input');
        const btnApplyDiscount = document.getElementById('btn-apply-discount');
        const ajusteValorRadios = document.getElementsByName('ajuste-valor');


        // Helper to format currency
        function formatCurrency(value) {
            return `R$ ${parseFloat(value).toFixed(2).replace('.', ',')}`;
        }
        function formatNumber(value, decimals = 4) {
            return parseFloat(value).toFixed(decimals).replace('.', ',');
        }
        function parseCurrency(value) {
            if (typeof value === 'number') return value;
            return parseFloat(value.replace('R$', '').replace(/\./g, '').replace(',', '.').trim());
        }
        function parseNumber(value, allowNegative = false) {
            if (typeof value === 'number') return value;
            let numStr = value.replace(/\./g, '').replace(',', '.').trim();
            let num = parseFloat(numStr);
            return isNaN(num) ? 0 : (allowNegative ? num : Math.max(0, num));
        }


        // Update item total display on input change
        function updateItemTotalDisplay() {
            const qty = parseNumber(itemQuantidadeInput.value);
            const unitPrice = parseNumber(itemValorUnitInput.value);
            itemValorTotalDisplay.value = formatCurrency(qty * unitPrice);
        }

        itemCodigoInput.addEventListener('change', () => {
            const product = products[itemCodigoInput.value];
            if (product) {
                itemSearchDisplay.value = `${itemCodigoInput.value} - ${product.name}`;
                itemValorUnitInput.value = formatNumber(product.price);
                itemQuantidadeInput.value = formatNumber(1, 0); // Default to 1
                updateItemTotalDisplay();
            } else {
                itemSearchDisplay.value = "Produto não encontrado";
                itemValorUnitInput.value = formatNumber(0);
                 updateItemTotalDisplay();
            }
        });
        itemQuantidadeInput.addEventListener('input', updateItemTotalDisplay);
        itemValorUnitInput.addEventListener('input', updateItemTotalDisplay);


        // Add item to list
        btnAddItem.addEventListener('click', () => {
            const codigo = itemCodigoInput.value;
            const product = products[codigo];
            const quantidade = parseNumber(itemQuantidadeInput.value);
            const valorUnit = parseNumber(itemValorUnitInput.value);

            if (!product || quantidade <= 0 || valorUnit <= 0) {
                alert("Por favor, insira um código de produto válido, quantidade e valor unitário.");
                return;
            }

            const newItem = {
                id: codigo,
                name: product.name,
                quantity: quantidade,
                unitPrice: valorUnit,
                discount: 0.00, // Item-specific discount (not implemented in UI)
                totalPrice: quantidade * valorUnit
            };
            saleItems.push(newItem);
            renderItemsList();
            updateSalesSummary();
            clearItemInputs();
        });

        function clearItemInputs() {
            itemCodigoInput.value = "";
            itemQuantidadeInput.value = formatNumber(1,0);
            itemValorUnitInput.value = formatNumber(0);
            itemSearchDisplay.value = "";
            updateItemTotalDisplay();
            itemCodigoInput.focus();
        }

        // Render items in the table
        function renderItemsList() {
            itemsListBody.innerHTML = ""; // Clear existing items
            saleItems.forEach((item, index) => {
                const row = itemsListBody.insertRow();
                row.innerHTML = `
                    <td>${index + 1}-Cod: <strong>${item.id}</strong> - ${item.name}</td>
                    <td>${formatNumber(item.quantity)}</td>
                    <td>${formatNumber(item.unitPrice)}</td>
                    <td>${formatNumber(item.discount)}</td>
                    <td><strong>${formatNumber(item.totalPrice, 2)}</strong></td>
                    <td class="actions"><i class="fas fa-ellipsis-h action-icon" onclick="removeItem(${index})"></i></td>
                `;
            });
        }
        
        window.removeItem = function(index) { // Make it global for inline onclick
            if (confirm('Tem certeza que deseja remover este item?')) {
                saleItems.splice(index, 1);
                renderItemsList();
                updateSalesSummary();
            }
        }


        // Update overall sales summary
        function updateSalesSummary() {
            const subtotal = saleItems.reduce((sum, item) => sum + item.totalPrice, 0);
            const totalItems = saleItems.length; // Or sum of quantities if preferred
            
            const ajusteTipo = document.querySelector('input[name="ajuste-valor"]:checked').value;
            let descontoValor = parseCurrency(valorDescontoInput.value);
            
            saleDiscount = (ajusteTipo === 'desconto') ? descontoValor : -descontoValor; // negativo para acréscimo

            const totalGeral = subtotal - saleDiscount;

            summaryTotalItems.textContent = totalItems;
            summarySubtotal.textContent = formatCurrency(subtotal);
            summaryDescontoGeral.textContent = formatCurrency(Math.abs(saleDiscount)); // Show absolute for display
            summaryTotalGeral.textContent = formatCurrency(totalGeral);
            updatePaymentSummary(); // Recalculate payment summary when total changes
        }
        
        btnApplyDiscount.addEventListener('click', updateSalesSummary);
        ajusteValorRadios.forEach(radio => radio.addEventListener('change', updateSalesSummary));
        valorDescontoInput.addEventListener('input', () => { // Live update if needed, or just on button click
             // For now, only update on button click or radio change
        });


        // Add payment
        btnAddPayment.addEventListener('click', () => {
            const valor = parseCurrency(valorPagoInput.value);
            const forma = formaPagamentoInput.value.trim();

            if (valor <= 0 || forma === "") {
                alert("Por favor, insira um valor pago válido e uma forma de pagamento.");
                return;
            }
            salePayments.push({ forma: forma, valor: valor });
            renderPaymentsList();
            updatePaymentSummary();
            valorPagoInput.value = "0,00";
            formaPagamentoInput.value = "DINHEIRO"; // Reset to default
        });

        // Render payments list
        function renderPaymentsList() {
            if (salePayments.length === 0) {
                paymentMethodsList.innerHTML = "Nenhum pagamento informado";
                return;
            }
            paymentMethodsList.innerHTML = "";
            salePayments.forEach(payment => {
                const div = document.createElement('div');
                div.innerHTML = `<span>${payment.forma}</span><span>${formatCurrency(payment.valor)}</span>`;
                paymentMethodsList.appendChild(div);
            });
        }

        // Update payment summary (Pago, Restante, Troco)
        function updatePaymentSummary() {
            const totalPago = salePayments.reduce((sum, p) => sum + p.valor, 0);
            const totalGeralVenda = parseCurrency(summaryTotalGeral.textContent);
            
            const restante = Math.max(0, totalGeralVenda - totalPago);
            const troco = Math.max(0, totalPago - totalGeralVenda);

            summaryPago.textContent = formatCurrency(totalPago);
            summaryRestante.textContent = formatCurrency(restante);
            summaryTroco.textContent = formatCurrency(troco);
        }


        // Initial setup
        document.addEventListener('DOMContentLoaded', () => {
            renderItemsList(); // Render initial items
            updateSalesSummary(); // Calculate initial summary
            updatePaymentSummary(); // Calculate initial payment details

            // Initialize item entry if first product is set
            const firstProductKey = Object.keys(products)[0];
            if (firstProductKey) {
                 itemCodigoInput.value = firstProductKey; // Set to first product for demo
                 itemCodigoInput.dispatchEvent(new Event('change')); // Trigger change to populate
            }
        });

        // Add event listeners to all action buttons to log their purpose
        document.querySelectorAll('.action-buttons-grid .btn').forEach(button => {
            button.addEventListener('click', (e) => {
                console.log(`Botão clicado: ${e.target.textContent.trim()}`);
                // Example: For "Tela Cheia (F11)"
                if (e.target.textContent.includes("Tela Cheia")) {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            alert(`Erro ao tentar tela cheia: ${err.message} (${err.name})`);
                        });
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        }
                    }
                }
            });
        });

    </script>
</body>
</html>