<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Garantias</title>
    <style>
    body {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }

    .content-wrapper {
        padding: 20px 0;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #eee;
        padding: 20px;
        border-radius: 12px 12px 0 0 !important;
    }

    .filter-form label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #555;
    }

    .table thead {
        background-color: #f1f3f9;
    }

    .table thead th {
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-aprovado {
        background-color: #e7f6ea;
        color: #0fb434;
    }

    .status-reprovado {
        background-color: #fbeae9;
        color: #e93339;
    }

    .status-pendente {
        background-color: #fff4e5;
        color: #ff9800;
    }

    .status-aberto {
        background-color: #e5f1ff;
        color: #007bff;
    }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-btn {
        min-width: 38px;
        height: 38px;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #007bff;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .page-btn.active {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .page-btn:disabled {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    .protocolo-link {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
    }

    .protocolo-link:hover {
        text-decoration: underline;
    }

    .hidden {
        display: none !important;
    }

    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    </style>
</head>

<body>

    <div id="loading-overlay" class="hidden">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="content-wrapper">
        <div class="container-fluid">

            <!-- Card de Filtros -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Consulta de Garantias</h4>
                </div>
                <div class="card-body">
                    <form id="filtroForm" class="row filter-form">
                        <div class="col-md-4 col-lg-3 mb-3">
                            <label for="codigoCliente">Código do Cliente</label>
                            <input type="text" id="codigoCliente" name="codigoCliente" class="form-control"
                                placeholder="Ex: 12345" value="<?php echo ($cliente > 0) ? $cliente : ''; ?>">
                        </div>
                        <div class="col-md-4 col-lg-3 mb-3">
                            <label for="codigoRCA">Código RCA (Vendedor)</label>
                            <input type="text" id="codigoRCA" name="codigoRCA" class="form-control" placeholder="Ex: 50"
                                value="<?php echo ($rca > 0) ? $rca : ''; ?>">
                        </div>
                        <div class="col-md-4 col-lg-3 mb-3 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="button" id="btnConsultar" class="btn btn-primary">Consultar</button>
                                <button type="button" id="btnLimpar" class="btn btn-outline-secondary">Limpar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card de Resultados -->
            <div class="card hidden" id="resultadosCard">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Protocolo</th>
                                    <th>Data</th>
                                    <th>Cód. Cliente</th>
                                    <th>Nome do Cliente</th>
                                    <th>Status</th>
                                    <th>Cód. RCA</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody id="corpoTabela"></tbody>
                        </table>
                    </div>

                    <div id="semResultados" class="alert alert-warning text-center hidden">
                        Nenhuma garantia encontrada com os filtros selecionados.
                    </div>

                    <div id="paginacaoContainer" class="pagination-container hidden">
                        <div class="pagination-info text-muted small">
                            Mostrando <span id="totalRegistros">0</span> registros encontrados.
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button id="btnAnterior" class="page-btn">«</button>
                            <div id="paginacaoNumeros" class="d-flex gap-1"></div>
                            <button id="btnProxima" class="page-btn">»</button>
                        </div>
                        <div class="page-size-selector">
                            <select id="selectPageSize" class="form-select form-select-sm">
                                <option value="10">10 por página</option>
                                <option value="25" selected>25 por página</option>
                                <option value="50">50 por página</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Variáveis de Sessão passadas do PHP para o JS ---
        const sessaoRca = <?php echo json_encode($rca); ?>;
        const sessaoCliente = <?php echo json_encode($cliente); ?>;

        const apiEndpoint = '/backend/routers/consulta_garantias.php';
        const filtroForm = document.getElementById('filtroForm');
        const btnConsultar = document.getElementById('btnConsultar');
        const btnLimpar = document.getElementById('btnLimpar');
        const corpoTabela = document.getElementById('corpoTabela');
        const resultadosCard = document.getElementById('resultadosCard');
        const semResultados = document.getElementById('semResultados');
        const loadingOverlay = document.getElementById('loading-overlay');
        const paginacaoContainer = document.getElementById('paginacaoContainer');

        let dadosCompletos = [];
        let paginaAtual = 1;
        let itensPorPagina = 25;

        // --- Eventos ---

        btnConsultar.addEventListener('click', () => {
            if (validarPermissao()) {
                consultarGarantias();
            }
        });

        btnLimpar.addEventListener('click', () => {
            filtroForm.reset();
            resultadosCard.classList.add('hidden');
            dadosCompletos = [];
        });

        // --- Funções ---

        function validarPermissao() {
            const inputRca = document.getElementById('codigoRCA').value.trim();
            const inputCliente = document.getElementById('codigoCliente').value.trim();

            /**
             * Lógica: 
             * Se houver um RCA na sessão (sessaoRca > 0):
             *   - Pode deixar em branco (vazio).
             *   - Se preencher, TEM que ser igual ao da sessão.
             */
            if (sessaoRca > 0 && inputRca !== "" && inputRca != sessaoRca) {
                alert("Atenção: Você só pode pesquisar o seu RCA (" + sessaoRca +
                    ") ou deixar o campo em branco.");
                document.getElementById('codigoRCA').value = sessaoRca;
                return false;
            }

            /**
             * Se houver um Cliente na sessão (sessaoCliente > 0):
             *   - Pode deixar em branco (vazio).
             *   - Se preencher, TEM que ser igual ao da sessão.
             */
            if (sessaoCliente > 0 && inputCliente !== "" && inputCliente != sessaoCliente) {
                alert("Atenção: Você só pode pesquisar o seu código de cliente (" + sessaoCliente +
                    ") ou deixar o campo em branco.");
                document.getElementById('codigoCliente').value = sessaoCliente;
                return false;
            }

            return true;
        }

        async function consultarGarantias() {
            const params = new URLSearchParams(new FormData(filtroForm));

            loadingOverlay.classList.remove('hidden');
            resultadosCard.classList.remove('hidden');
            corpoTabela.innerHTML = '';
            semResultados.classList.add('hidden');
            paginacaoContainer.classList.add('hidden');

            try {
                const response = await fetch(`${apiEndpoint}?${params.toString()}`);
                const result = await response.json();

                if (result.success) {
                    dadosCompletos = result.data;
                    document.getElementById('totalRegistros').textContent = result.count;

                    if (dadosCompletos.length > 0) {
                        irParaPagina(1);
                        paginacaoContainer.classList.remove('hidden');
                    } else {
                        semResultados.classList.remove('hidden');
                    }
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao conectar com o servidor.');
            } finally {
                loadingOverlay.classList.add('hidden');
            }
        }

        function irParaPagina(pagina) {
            paginaAtual = pagina;
            const inicio = (pagina - 1) * itensPorPagina;
            const fim = inicio + itensPorPagina;
            const dadosPagina = dadosCompletos.slice(inicio, fim);
            renderizarTabela(dadosPagina);
            renderizarControlesPagina();
        }

        function renderizarTabela(dados) {
            corpoTabela.innerHTML = '';
            dados.forEach(item => {
                const statusLower = item.status ? item.status.toLowerCase() : '';
                let statusClass = 'status-aberto';
                if (statusLower.includes('aprovado')) statusClass = 'status-aprovado';
                else if (statusLower.includes('reprovado')) statusClass = 'status-reprovado';
                else if (statusLower.includes('pendente')) statusClass = 'status-pendente';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td><a href="modules/relatorios/rptGarFull.php?id=${item.protocolo}" target="_blank" class="protocolo-link">${item.protocolo}</a></td>
                        <td>${item.dt_cad}</td>
                        <td>${item.CODCLI}</td>
                        <td class="small">${item.CLIENTE}</td>
                        <td><span class="status-badge ${statusClass}">${item.status}</span></td>
                        <td>${item.codusur}</td>
                        <td class="small text-muted">${item.NOME}</td>
                    `;
                corpoTabela.appendChild(tr);
            });
        }

        function renderizarControlesPagina() {
            const totalPaginas = Math.ceil(dadosCompletos.length / itensPorPagina);
            const containerNumeros = document.getElementById('paginacaoNumeros');
            containerNumeros.innerHTML = '';

            document.getElementById('btnAnterior').disabled = (paginaAtual === 1);
            document.getElementById('btnProxima').disabled = (paginaAtual === totalPaginas || totalPaginas ===
                0);

            let start = Math.max(1, paginaAtual - 2);
            let end = Math.min(totalPaginas, start + 4);

            for (let i = start; i <= end; i++) {
                const btn = document.createElement('button');
                btn.className = `page-btn ${i === paginaAtual ? 'active' : ''}`;
                btn.textContent = i;
                btn.onclick = () => irParaPagina(i);
                containerNumeros.appendChild(btn);
            }
        }

        document.getElementById('selectPageSize').addEventListener('change', function() {
            itensPorPagina = parseInt(this.value);
            irParaPagina(1);
        });

        document.getElementById('btnAnterior').addEventListener('click', () => {
            if (paginaAtual > 1) irParaPagina(paginaAtual - 1);
        });
        document.getElementById('btnProxima').addEventListener('click', () => {
            const totalPaginas = Math.ceil(dadosCompletos.length / itensPorPagina);
            if (paginaAtual < totalPaginas) irParaPagina(paginaAtual + 1);
        });
    });
    </script>
</body>

</html>