<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - Ponto de Venda</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="pdv-container">
        <header class="pdv-header">
            <h1>Diagrammato</h1>
        </header>

        <div class="pdv-main">
            <section class="produtos-section">
                <div class="section-header">
                    <h2>Código</h2>
                    <div class="valores-header">
                        <span>Quantidade</span>
                        <span>Valor Unit</span>
                        <span>Valor total</span>
                    </div>
                </div>

                <div class="valores-destaque">
                    <span class="moeda">R$</span>
                    <span class="valor">64</span>
                    <div class="valores-lista">
                        <div><span>1,000.00</span></div>
                        <div><span>7,200.00</span></div>
                        <div><span class="total">R$720</span></div>
                    </div>
                </div>

                <div class="itens-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qtd</th>
                                <th>Valor unit.</th>
                                <th>Desc.</th>
                                <th>Valor total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 - Code 1060 - TELHA SANDUCHENI</td>
                                <td>1,000.00</td>
                                <td>149,38</td>
                                <td>0.00</td>
                                <td>149,38</td>
                                <td class="acoes">**</td>
                            </tr>
                            <tr>
                                <td>2 - Code 1180 - CHAPA EBOXERO XXXXXX NO</td>
                                <td>1,000.00</td>
                                <td>12,50</td>
                                <td>0.00</td>
                                <td>12,50</td>
                                <td class="acoes">**</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="cliente-section">
                <div class="section-header">
                    <h2>Programento</h2>
                </div>

                <div class="cliente-info">
                    <div class="info-row">
                        <div class="info-group">
                            <label>Código</label>
                            <div class="info-content">
                                <span>Cliente</span>
                                <span>Tipo de documento</span>
                                <span>Documento</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <label>Vendedor</label>
                            <div class="info-content">
                                <span>Entrega</span>
                                <span>Observação</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-group full-width">
                            <label>Página Junior</label>
                            <div class="info-content">
                                <span>Entregar no Endereço</span>
                                <span>Insert* Observação</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-group full-width">
                            <label>Observação</label>
                            <div class="info-content">
                                <textarea></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pagamento-section">
                <div class="section-header">
                    <h2>Forma de Pagamento</h2>
                </div>

                <div class="pagamento-info">
                    <div class="pagamento-row">
                        <div class="pagamento-group">
                            <label>Valor Pago</label>
                            <div class="pagamento-content">
                                <span>DINHEIRO</span>
                                <span>0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="pagamento-row">
                        <div class="pagamento-group">
                            <label>Test Pagamento</label>
                            <div class="pagamento-content">
                                <span>Valor Pago</span>
                                <span>Alastre do valor</span>
                                <span>Desconto</span>
                                <span>Acréscimo</span>
                            </div>
                        </div>
                    </div>

                    <div class="pagamento-row">
                        <div class="pagamento-group">
                            <label>Nenhum pagamento informado</label>
                            <div class="pagamento-content">
                                <span>Tipo de desconto</span>
                                <span>Valor do desconto</span>
                                <span>Valor R$</span>
                                <span>R$ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="resumo-section">
                <div class="resumo-row">
                    <div class="resumo-group">
                        <label>Resumo</label>
                        <div class="resumo-content">
                            <span>Pago</span>
                            <span>Resume</span>
                            <span>R$ 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="resumo-row">
                    <div class="resumo-group">
                        <label>Recurso desta venda</label>
                        <div class="resumo-content">
                            <span>Base</span>
                            <span>Sub final</span>
                            <span>Desconto</span>
                            <span>R$ 161,88</span>
                            <span>0.00</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="pdv-footer">
            <div class="botoes-principais">
                <button class="btn btn-primary">Resultar (P1)</button>
                <button class="btn btn-success">Finalizar (P2)</button>
                <button class="btn btn-info">PV (P3)</button>
                <button class="btn btn-warning">Contabilir (venda/EP3)</button>
                <button class="btn btn-danger">Cancelar (venda/EP3)</button>
                <button class="btn btn-secondary">Cancelar Item (P7)</button>
            </div>

            <div class="botoes-secundarios">
                <button class="btn btn-sm">Financeiro</button>
                <button class="btn btn-sm">Posicion gratificados</button>
                <button class="btn btn-sm">Tender Tipo de Errado</button>
                <button class="btn btn-sm">Test-Otada (P11)</button>
                <button class="btn btn-sm">Self (ESC)</button>
            </div>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>