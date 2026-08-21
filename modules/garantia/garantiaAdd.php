<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

    * {
        box-sizing: border-box;
        font-family: 'roboto', ui-sans-serif, system-ui, sans-serif;
    }

    .card {
        box-shadow: 0 4px 10px rgba(223, 213, 213, 0.05);
        border-radius: 12px;
    }

    .btn-roxo {
        background-color: #7A5AF8;
        color: white;
    }

    .btn-roxo:hover {
        background-color: #674ED7;
    }

    .min-height-50 {
        min-height: 50px;
    }

    .descricao-texto1 {
        margin-top: 0.0rem;
        margin-bottom: 0;
        align-self: center;
    }

    .descricao-cliente {
        margin-top: 0.5rem;
        margin-bottom: 0;
        align-self: center;
    }

    .descricao-produto {
        margin-top: 0.5rem;
        margin-bottom: 0;
        align-self: center;
    }

    /* Adicione isso ao seu arquivo CSS */
    #previewContainer .card {
        transition: all 0.3s ease;
    }

    #previewContainer .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-img-top img,
    .card-img-top video {
        object-fit: contain;
        width: 100%;
    }

    /* Estilos para o upload mobile */
    #mobileSourceSelector .btn {
        padding: 15px;
        font-size: 16px;
        border: 2px solid;
    }

    #mobileSourceSelector .bi {
        font-size: 24px;
        margin-right: 10px;
    }

    /* Cores específicas para cada botão */
    #useCameraBtn {
        border-color: #0d6efd;
        color: #0d6efd;
    }

    #useVideoBtn {
        border-color: #0dcaf0;
        color: #0dcaf0;
    }

    #useGalleryBtn {
        border-color: #6c757d;
        color: #6c757d;
    }

    #useCameraBtn:hover {
        background-color: #0d6efd;
        color: white;
    }

    #useVideoBtn:hover {
        background-color: #0dcaf0;
        color: white;
    }

    #useGalleryBtn:hover {
        background-color: #6c757d;
        color: white;
    }

    /* Melhorias na visualização das imagens */
    .card-img-top img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .card-img-top video {
        width: 100%;
        height: 100px;
        object-fit: cover;
        background-color: #000;
    }

    /* Ajustes para mobile */
    @media (max-width: 768px) {
        #previewContainer .col-6 {
            width: 50%;
        }

        .modal-dialog {
            margin: 5px;
        }

        .modal-body {
            padding: 15px;
        }
    }

    #autocomplete-list {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .autocomplete-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .autocomplete-item:hover {
        background-color: #f8f9fa;
    }

    .autocomplete-item strong {
        color: #7A5AF8;
    }

    /* Garante que o container do input seja a referência para o posicionamento */
    .search-container {
        position: relative;
    }


    .list-group {
        background: white;
        border: 1px solid #ddd;
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="container-fluid">

                <h3 class="p-2">Nova Garantia</h3>

                <!-- Card Cliente -->
                <div class="card p-3 mb-4">
                    <h5>Pesquisar Cliente</h5>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-10 col-9 search-container" style="position: relative;">
                            <input type="text" class="form-control" id="codCliente" placeholder="Digite CODCLI ou CNPJ"
                                autocomplete="off">
                        </div>
                        <div class="col-md-2 col-3">
                            <button type="button" id="btnConsultarCliente" class="btn btn-outline-secondary w-100 py-2"
                                title="Consultar">
                                <i class="bi bi-search"></i>
                                <span class="d-none d-xl-inline">&nbsp; Consultar</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="descricao-texto1"><label>Cliente</label></p>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-10 col-9">
                            <p class="descricao-cliente" id="descCliente"><label><strong>Nome do Cliente será mostrado
                                        aqui para confirmação</strong></label></p>
                        </div>
                        <div class="col-md-2 col-3">
                            <button type="button" id="btnExcluirCliente" class="btn btn-outline-danger w-100 py-2"
                                title="Excluir Cliente">
                                <i class="bi bi-trash"></i>
                                <span class="d-none d-xl-inline">&nbsp; Excluir Cliente?</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Itens Garantia -->
                <div class="card p-3 mb-4">
                    <h5>Itens da Garantia</h5>

                    <div class="row g-2 align-items-center mb-3 w-100">
                        <div class="col-12 col-md-2 search-container" style="position: relative;">
                            <label>Código Produto</label>
                            <input type="text" class="form-control" id="codpro" placeholder="Ex: 12220-BF0-CG08"
                                maxlength="14" required autocomplete="off">
                            <div id="autocomplete-product-list" class="list-group"
                                style="position: absolute; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: none;">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <p class="descricao-texto1"><label>Descrição</label></p>
                            <p class="descricao-produto" id="descpro"><label><strong>Nome do Produto para
                                        confirmação</strong></label></p>
                        </div>
                        <div class="col-12 col-md-1">
                            <label>Qtd.</label>
                            <input type="number" class="form-control" min="1" value="1" id="qtd"
                                onkeydown="return event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 109"
                                oninput="this.value = this.value.replace(/[eE\-]/g, '')" required>
                        </div>
                        <script>
                            $(document).ready(function() {
                                $('#qtd').on('input', function() {
                                    let value = $(this).val();
                                    // Remove qualquer caractere negativo
                                    value = value.replace(/[eE\-]/g, '');

                                    // Garante que o valor mínimo seja 1
                                    if (value < 1) {
                                        value = 1;
                                    }

                                    $(this).val(value);
                                });

                                $('#qtd').on('keydown', function(e) {
                                    // Bloqueia a tecla 'E' (expoente), '-' (hífen) e tecla de menos do numpad
                                    if (e.keyCode === 69 || e.keyCode === 189 || e.keyCode === 109) {
                                        e.preventDefault();
                                        return false;
                                    }
                                });
                            });
                        </script>
                        <div class="col-12 col-md-2">
                            <label>Motivo</label>
                            <select id="motivoSelect" class="form-select" required>
                                <option value="">Selecione um motivo...</option>
                                <option>Arranhado</option>
                                <option>Com ruídos</option>
                                <option>Descentralizado</option>
                                <option>Deslizando</option>
                                <option>Falha na aceleração</option>
                                <option>Falha na vulcanização</option>
                                <option>Falhando</option>
                                <option>Fumaçando</option>
                                <option>Não aciona</option>
                                <option>Não encaixa</option>
                                <option>Não injeta combustível</option>
                                <option>Não regula</option>
                                <option>Oscilando</option>
                                <option>Sem corrente elétrica</option>
                                <option>Sem pressão</option>
                                <option>Sem pulso</option>
                                <option>Travando</option>
                                <option>Vazando</option>
                                <option>Outros</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label>Observação</label>
                            <input type="text" class="form-control" placeholder="Observação" id="observacao" required>
                        </div>
                        <div class="col-12 col-md-2 d-flex align-items-end min-height-50 mt-4 p-0">
                            <div class="d-flex w-100 gap-2">
                                <button class="btn btn-primary flex-grow-1" title="Upload" id="uploadFilesGarantia"
                                    required>
                                    <i class="bi bi-upload"></i>
                                    <span class="d-none d-xl-inline">&nbsp; Foto/Vídeo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela Itens -->
                    <div class="table-responsive">
                        <table id="tabelaItens"
                            style="width: 100%; padding: 1px; background-color: #f2f2f2; border: none; border-radius: 10px; border-collapse: separate; border-spacing: 10;">
                            <thead class="table-light">
                                <tr>
                                    <th class="d-none d-md-table-cell"
                                        style="border-top-left-radius: 10px; padding: 10px;">#</th>
                                    <th>Código</th>
                                    <th class="d-none d-md-table-cell">Descrição</th>
                                    <th class="d-none d-md-table-cell">Qtd.</th>
                                    <th class="d-none d-md-table-cell">Motivo</th>
                                    <th class="d-none d-md-table-cell">Observação</th>
                                    <th class="d-none d-md-table-cell">Anexos</th>
                                    <th style="border-top-right-radius: 10px;">Excluir</th>
                                </tr>
                            </thead>
                            <tbody id="itensBody" style="background-color: #ffffff; padding: 10px;">
                                <!-- Itens serão adicionados dinamicamente aqui -->
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card p-3 mb-4">
                    <div class="row g-2">
                        <div class="col-12 col-sm-auto">
                            <button class="btn btn-secondary w-100" id="gravarPedido">Gravar Pedido [Em
                                Digitação]</button>
                        </div>
                        <div class="col-12 col-sm-auto">
                            <button class="btn btn-roxo w-100" id="finalizarPedido">Finalizar Pedido [Enviar]</button>
                        </div>
                        <div class="col-12 col-sm-auto">
                            <button class="btn btn-outline-warning w-100" id="limparDadosTemp">Limpar Dados
                                Temporários</button>
                        </div>
                    </div>
                </div>

                <link rel="stylesheet"
                    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

            </div>
        </div>
    </div>
</div>

<!-- Modal para upload de arquivos -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Anexar Fotos/Vídeos</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <!-- Seletor de origem - aparece apenas em dispositivos móveis -->
                <div class="mb-3" id="mobileSourceSelector">
                    <label class="form-label">Escolha a origem:</label>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" id="useCameraBtn">
                            <i class="bi bi-camera"></i> Tirar Foto
                        </button>
                        <button type="button" class="btn btn-outline-info" id="useVideoBtn">
                            <i class="bi bi-camera-video"></i> Gravar Vídeo
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="useGalleryBtn">
                            <i class="bi bi-images"></i> Escolher da Galeria
                        </button>
                    </div>
                </div>

                <!-- Input de arquivo tradicional - aparece apenas em desktop -->
                <div class="mb-3 d-none p-3" id="desktopFileInput">
                    <label class="form-label">Selecione os arquivos:</label>
                    <input type="file" class="form-control" id="multiFileUpload" accept="image/*,video/*" multiple>
                </div>
                <p>
                    <b>Permitido por garantia até 250 Mb, portanto não inclua videos e fotos com mais de 10 Mb</b>
                </p>
                <div class="row mt-3 p-3" id="previewContainer">
                    <!-- As miniaturas serão exibidas aqui -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="finalizarUpload">Finalizar Upload</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {


        $('#qtd, #motivoSelect, #observacao').on('change', function() {
            saveToTempStorage();
        });

        // ========== FUNÇÕES AUXILIARES E FORMATADORES ==========

        function generateSessionId() {
            return 'garantia_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function formatCNPJ(cnpj) {
            const digits = cnpj.replace(/\D/g, '');
            if (digits.length !== 14) return cnpj;
            return `${digits.substring(0, 2)}.${digits.substring(2, 5)}.${digits.substring(5, 8)}/${digits.substring(8, 12)}-${digits.substring(12)}`;
        }

        function generateUniqueItemId() {
            const timestamp = Date.now();
            const sequence = itemSequenceCounter++;
            return `${timestamp}_${sequence}`;
        }

        function generateFileName(originalName, productCode, anexoId) {
            const extension = originalName.split('.').pop().toLowerCase();
            const now = new Date();
            const dateStr = now.toISOString().split('T')[0].replace(/-/g, '');
            const timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '');
            return `${productCode}_${dateStr}_${timeStr}_${anexoId}.${extension}`;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                window.innerWidth <= 768;
        }

        // ========== INICIALIZAÇÃO DE VARIÁVEIS DE SESSÃO ==========

        let sessionId = localStorage.getItem('active_garantia_session') || generateSessionId();
        localStorage.setItem('active_garantia_session', sessionId);

        const sessaoCodCli = "<?php echo $cliente; ?>";
        const sessaoCodUsur = "<?php echo $codusur1; ?>";
        const isSuperUser = (sessaoCodCli == "0" || sessaoCodCli == "") && (sessaoCodUsur == "0" || sessaoCodUsur ==
            "");

        let sessaoGrupoPrincipal = null;
        let garantiaItens = [];
        let itemSequenceCounter = 0;
        let currentAnexoId = 0;
        let selectedFiles = [];

        // Descobrir o grupo do usuário logado
        function buscarGrupoUsuarioLogado() {
            if (!sessaoCodCli) return;
            $.ajax({
                url: `<?php echo $url_backend; ?>/routers/clientes?CODCLI=${sessaoCodCli}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        sessaoGrupoPrincipal = String(response.data[0].CODCLIPRINC);
                        console.log("Seu Grupo Principal identificado:", sessaoGrupoPrincipal);
                    }
                }
            });
        }
        buscarGrupoUsuarioLogado();

        // ========== LOGICA DE ARMAZENAMENTO TEMPORÁRIO NO SERVIDOR ==========

        function saveToTempStorage() {
            const tempData = {
                sessionId: sessionId,
                timestamp: new Date().toISOString(),
                cliente: {
                    codCliente: $('#codCliente').val(),
                    clienteData: $('#codCliente').data('cliente-data'),
                    descCliente: $('#descCliente').text()
                },
                draft: {
                    codpro: $('#codpro').val(),
                    qtd: $('#qtd').val(),
                    motivo: $('#motivoSelect').val(),
                    observacao: $('#observacao').val()
                },
                garantiaItens: garantiaItens,
                currentAnexoId: currentAnexoId
            };

            $.ajax({
                url: 'modules/garantia/save_temp_garantia.php',
                type: 'POST',
                data: {
                    session_id: sessionId,
                    temp_data: JSON.stringify(tempData)
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) console.log('Backup salvo em garantiaTemp/');
                }
            });
        }

        function loadFromTempStorage() {
            $.ajax({
                url: 'modules/garantia/load_temp_garantia.php',
                type: 'GET',
                data: {
                    session_id: sessionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success && response.temp_data) {
                        try {
                            const tempData = JSON.parse(response.temp_data);
                            restoreFromTempData(tempData);
                        } catch (e) {
                            console.error('Erro ao ler JSON temporário');
                        }
                    }
                }
            });
        }

        function restoreFromTempData(tempData) {
            if (!tempData) return;
            if (tempData.cliente) {
                $('#codCliente').val(tempData.cliente.codCliente);
                if (tempData.cliente.clienteData) {
                    $('#codCliente').data('cliente-data', tempData.cliente.clienteData);
                    $('#codCliente').data('codusur1', tempData.cliente.clienteData.CODUSUR1);
                }
                $('#descCliente').html('<strong>' + tempData.cliente.descCliente + '</strong>');
            }

            if (tempData.draft) {
                $('#codpro').val(tempData.draft.codpro);
                $('#qtd').val(tempData.draft.qtd);
                $('#motivoSelect').val(tempData.draft.motivo);
                $('#observacao').val(tempData.draft.observacao);

                // Se houver produto, tenta buscar a descrição dele de novo
                if (tempData.draft.codpro) {
                    searchProduct(tempData.draft.codpro);
                }
            }


            if (tempData.garantiaItens && tempData.garantiaItens.length > 0) {
                garantiaItens = tempData.garantiaItens;
                currentAnexoId = tempData.currentAnexoId || 0;
                updateItensTable();
                Swal.fire({
                    title: 'Dados Recuperados',
                    text: 'Sua sessão anterior foi restaurada.',
                    icon: 'info',
                    timer: 2000
                });
            }
            checkFields();
        }

        function clearTempStorage() {
            $.ajax({
                url: 'modules/garantia/clear_temp_garantia.php',
                type: 'POST',
                data: {
                    session_id: sessionId
                },
                success: function() {
                    localStorage.removeItem('active_garantia_session');
                }
            });
        }

        // ========== PESQUISA E AUTOCOMPLETE DE CLIENTES ==========

        const clientInput = $('#codCliente');
        const clientNameLabel = $('#descCliente');
        if ($('#autocomplete-list').length === 0) {
            clientInput.after(
                '<div id="autocomplete-list" class="list-group" style="position: absolute; z-index: 1000; width: 95%; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>'
            );
        }
        const autocompleteList = $('#autocomplete-list');

        clientInput.on('input', function() {
            const query = $(this).val().trim();
            if (query.length < 2) {
                autocompleteList.hide();
                return;
            }

            let endpoint = query.replace(/\D/g, '').length > 6 ?
                `<?php echo $url_backend; ?>/routers/clientes?CGCENT=${encodeURIComponent(query)}` :
                `<?php echo $url_backend; ?>/routers/clientes?CODCLI=${encodeURIComponent(query)}`;

            $.ajax({
                url: endpoint,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        autocompleteList.empty().show();
                        response.data.slice(0, 10).forEach(item => {
                            const suggestion = $(`<a href="javascript:void(0);" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex w-100 justify-content-between"><small class="mb-1 text-primary"><strong>${item.CODCLI}</strong></small><small class="text-muted">${item.CGCENT || ''}</small></div>
                            <div style="font-size: 0.85rem;">${item.CLIENTE}</div></a>`);
                            suggestion.on('click', function() {
                                clientInput.val(item.CODCLI);
                                autocompleteList.hide();
                                searchClient(item.CODCLI);
                            });
                            autocompleteList.append(suggestion);
                        });
                    } else {
                        autocompleteList.hide();
                    }
                }
            });
        });

        $('#btnConsultarCliente').on('click', function() {
            searchClient(clientInput.val().trim());
        });

        function searchClient(query) {
            if (!query) return;
            let endpoint = query.replace(/\D/g, '').length > 6 ?
                `<?php echo $url_backend; ?>/routers/clientes?CGCENT=${encodeURIComponent(formatCNPJ(query))}` :
                `<?php echo $url_backend; ?>/routers/clientes?CODCLI=${encodeURIComponent(query)}`;

            $.ajax({
                url: endpoint,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        const cli = response.data[0];

                        // Validação de Segurança
                        if (!isSuperUser) {
                            const mesmoGrupo = (sessaoGrupoPrincipal && String(cli.CODCLIPRINC) ===
                                sessaoGrupoPrincipal);
                            const proprio = (String(cli.CODCLI) === sessaoCodCli);
                            const mesmoVend = (sessaoCodUsur && String(cli.CODUSUR1) === sessaoCodUsur);

                            if (!mesmoGrupo && !proprio && !mesmoVend) {
                                Swal.fire({
                                    title: 'Acesso Negado',
                                    text: 'Você não tem permissão para este cliente.',
                                    icon: 'error'
                                });
                                clientInput.val('');
                                clientNameLabel.find('strong').text(
                                    'Nome do Cliente será mostrado aqui');
                                return;
                            }
                        }

                        clientInput.val(cli.CODCLI).data('cliente-data', cli).data('codusur1', cli
                            .CODUSUR1);
                        clientNameLabel.html('<strong>' + cli.CLIENTE + '</strong>');
                        saveToTempStorage();
                        checkFields();
                    } else {
                        clientNameLabel.find('strong').text('Cliente não encontrado');
                    }
                }
            });
        }

        // ========== PESQUISA E AUTOCOMPLETE DE PRODUTOS ==========

        const productInput = $('#codpro');
        const productDescLabel = $('#descpro label strong');
        const productAutocompleteList = $('#autocomplete-product-list');

        productInput.on('input', function() {
            // Máscara CODFAB
            let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            let masked = "";
            if (value.length > 0) {
                masked += value.substring(0, 5);
                if (value.length > 5) masked += "-" + value.substring(5, 8);
                if (value.length > 8) masked += "-" + value.substring(8, 12);
            }
            $(this).val(masked.substring(0, 14));

            const query = $(this).val().trim();
            if (query.length < 3) {
                productAutocompleteList.hide();
                return;
            }

            $.ajax({
                url: `<?php echo $url_backend; ?>/routers/produtos?CODFAB=${encodeURIComponent(query)}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        productAutocompleteList.empty().show();
                        response.data.slice(0, 10).forEach(item => {
                            const suggestion = $(`<a href="javascript:void(0);" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex w-100 justify-content-between"><small class="text-primary"><strong>${item.CODFAB}</strong></small></div>
                            <div style="font-size: 0.8rem;">${item.DESCRICAO}</div></a>`);
                            suggestion.on('click', function() {
                                productInput.val(item.CODFAB);
                                productAutocompleteList.hide();
                                searchProduct(item.CODFAB);
                            });
                            productAutocompleteList.append(suggestion);
                        });
                    }
                }
            });
        });

        function searchProduct(query) {
            $.ajax({
                url: `<?php echo $url_backend; ?>/routers/produtos?CODFAB=${encodeURIComponent(query)}`,
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.data.length > 0) {
                        const prod = res.data[0];
                        productDescLabel.text(prod.DESCRICAO);
                        productInput.data('produto-data', prod);
                        checkFields();
                    }
                }
            });
        }

        // ========== LÓGICA DE UPLOAD (CÂMERA, VÍDEO, GALERIA) ==========

        const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

        $('#uploadFilesGarantia').on('click', function() {
            if (!productInput.val()) {
                Swal.fire('Aviso', 'Insira um produto primeiro.', 'warning');
                return;
            }
            if (isMobileDevice()) {
                $('#mobileSourceSelector').removeClass('d-none');
                $('#desktopFileInput').addClass('d-none');
            } else {
                $('#mobileSourceSelector').addClass('d-none');
                $('#desktopFileInput').removeClass('d-none');
            }
            uploadModal.show();
        });

        function capture(type) {
            const input = document.createElement('input');
            input.type = 'file';
            if (type === 'foto') {
                input.accept = 'image/*';
                input.capture = 'camera';
            }
            if (type === 'video') {
                input.accept = 'video/*';
                input.capture = 'camcorder';
            }
            if (type === 'galeria') {
                input.accept = 'image/*,video/*';
                input.multiple = true;
            }

            input.onchange = function(e) {
                const files = Array.from(e.target.files);
                selectedFiles = [...selectedFiles, ...files];
                showFilePreviews();
            };
            input.click();
        }

        $('#useCameraBtn').on('click', () => capture('foto'));
        $('#useVideoBtn').on('click', () => capture('video'));
        $('#useGalleryBtn').on('click', () => capture('galeria'));
        $('#multiFileUpload').on('change', function(e) {
            selectedFiles = [...selectedFiles, ...Array.from(e.target.files)];
            showFilePreviews();
        });

        function showFilePreviews() {
            const container = $('#previewContainer').empty();
            selectedFiles.forEach((file, index) => {
                const col = $(`
            <div class="col-6 col-md-3 mb-2">
                <div class="card p-1">
                    <div class="preview-box" style="height:100px; overflow:hidden; background:#000; display:flex; align-items:center; justify-content:center;">
                    </div>
                    <button class="btn btn-danger btn-sm w-100 mt-1">Remover</button>
                </div>
            </div>
        `);

                col.find('button').on('click', () => {
                    selectedFiles.splice(index, 1);
                    showFilePreviews();
                });

                const previewBox = col.find('.preview-box');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewBox.html(
                            `<img src="${e.target.result}" style="width:100%; height:100%; object-fit: cover;">`
                        );
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    // Cria uma URL temporária para o vídeo selecionado
                    const videoUrl = URL.createObjectURL(file);
                    previewBox.html(`
                <video src="${videoUrl}" style="width:100%; height:100%; object-fit: cover;" controls>
                    Seu navegador não suporta vídeos.
                </video>
            `);
                }

                container.append(col);
            });
        }

        $('#finalizarUpload').on('click', function() {
            const cod = productInput.val();
            const qtd = $('#qtd').val();
            const mot = $('#motivoSelect').val();
            const obs = $('#observacao').val();
            if (!cod || qtd <= 0 || !mot) {
                Swal.fire('Erro', 'Preencha Produto, Quantidade e Motivo.', 'error');
                return;
            }

            addItemToTable(cod, qtd, mot, obs, selectedFiles);
            uploadModal.hide();
            // Reset campos de inserção
            productInput.val('').removeData('produto-data');
            productDescLabel.text('Nome do Produto para confirmação');
            $('#qtd').val(1);
            $('#motivoSelect').val('');
            $('#observacao').val('');
            selectedFiles = [];
            $('#previewContainer').empty();
            saveToTempStorage();
        });

        // ========== MANIPULAÇÃO DA TABELA DE ITENS ==========

        function addItemToTable(codprod, qtd, motivo, observacao, anexos) {
            const itemId = generateUniqueItemId();
            const desc = productDescLabel.text();
            const newItem = {
                id: itemId,
                codprod,
                codfab: codprod,
                qtd,
                motivo,
                observacao,
                descricao: desc,
                anexos: []
            };

            let loaded = 0;
            if (anexos.length === 0) {
                pushItem();
            }

            anexos.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentAnexoId++;
                    newItem.anexos.push({
                        id: currentAnexoId,
                        fk_item: itemId,
                        anexo: generateFileName(file.name, codprod, currentAnexoId),
                        data: e.target.result,
                        type: file.type
                    });
                    loaded++;
                    if (loaded === anexos.length) pushItem();
                };
                reader.readAsDataURL(file);
            });

            function pushItem() {
                garantiaItens.unshift(newItem);
                updateItensTable();
                saveToTempStorage();
            }
        }

        function updateItensTable() {
            const tbody = $('#itensBody').empty();
            garantiaItens.forEach((item, index) => {
                const row = $(`<tr>
                <td class="d-none d-md-table-cell">${garantiaItens.length - index}</td>
                <td>${item.codprod}</td>
                <td class="d-none d-md-table-cell">${item.descricao}</td>
                <td class="d-none d-md-table-cell">${item.qtd}</td>
                <td class="d-none d-md-table-cell">${item.motivo}</td>
                <td class="d-none d-md-table-cell">${item.observacao || '-'}</td>
                <td class="d-none d-md-table-cell">${item.anexos.length} arq.</td>
                <td align="center"><button class="btn btn-sm btn-outline-danger" onclick="removerItemTabela('${item.id}')"><i class="bi bi-trash"></i></button></td>
            </tr>`);
                tbody.append(row);
            });
        }

        window.removerItemTabela = function(id) {
            Swal.fire({
                title: 'Remover item?',
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) {
                    garantiaItens = garantiaItens.filter(i => i.id !== id);
                    updateItensTable();
                    saveToTempStorage();
                }
            });
        };

        // ========== GRAVAÇÃO FINAL (JSON NA RAIZ) ==========

        $('#gravarPedido, #finalizarPedido').on('click', function(e) {
            e.preventDefault();

            const isFinalizar = $(this).attr('id') === 'finalizarPedido';
            const cliData = $('#codCliente').data('cliente-data');

            if (!cliData) {
                Swal.fire({
                    title: 'Atenção',
                    text: 'Selecione um cliente primeiro.',
                    icon: 'warning'
                });
                return;
            }
            if (garantiaItens.length === 0) {
                Swal.fire({
                    title: 'Atenção',
                    text: 'Adicione itens à garantia.',
                    icon: 'warning'
                });
                return;
            }


            const statusFinal = isFinalizar ? 'Aberto' : 'Em Digitacao';

            // 2. Adicione o campo status aqui no objeto
            const garantiaData = {
                garantia: {
                    id: Date.now(),
                    dt_cad: new Date().toISOString(),
                    codcli: cliData.CODCLI,
                    codusur: cliData.CODUSUR1 || '<?php echo $rca; ?>',
                    responsavel: 'Time Garantia',
                    status: statusFinal, // <--- ADICIONE ESTA LINHA
                    dispositivo: window.innerWidth <= 768 ? 'mobile' : 'computador'
                },
                garantia_itens: [],
                garantia_itens_anexos: []
            };

            garantiaItens.forEach(item => {
                garantiaData.garantia_itens.push({
                    id: item.id,
                    fk_garantia: garantiaData.garantia.id,
                    codcli: cliData.CODCLI,
                    codprod: item.codprod,
                    codfab: item.codfab,
                    qtd: item.qtd,
                    motivo: item.motivo,
                    observacao: item.observacao || null
                });
                item.anexos.forEach(anexo => {
                    garantiaData.garantia_itens_anexos.push({
                        id: anexo.id,
                        fk_item: item.id,
                        anexo: anexo.anexo
                    });
                });
            });

            // 1. Prepara a lista de uploads de arquivos NOVOS
            const uploads = [];
            garantiaItens.forEach(item => {
                item.anexos.forEach(anexo => {
                    // Só faz upload se for Base64 (ignora "FILE_SAVED")
                    if (anexo.data && typeof anexo.data === 'string' && anexo.data
                        .startsWith('data:')) {
                        const byteString = atob(anexo.data.split(',')[1]);
                        const mimeString = anexo.data.split(',')[0].split(':')[1].split(
                            ';')[0];
                        const ab = new ArrayBuffer(byteString.length);
                        const ia = new Uint8Array(ab);
                        for (let i = 0; i < byteString.length; i++) ia[i] = byteString
                            .charCodeAt(i);
                        const file = new File([new Blob([ab], {
                            type: mimeString
                        })], anexo.anexo, {
                            type: mimeString
                        });

                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('filename', anexo.anexo);

                        uploads.push($.ajax({
                            url: 'modules/garantia/upload.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false
                        }));
                    }
                });
            });

            // 2. Executa uploads (se houver) e depois salva o JSON
            if (uploads.length > 0) {
                // $.when gerencia múltiplos AJAX no jQuery
                $.when.apply($, uploads).always(function() {
                    saveGarantiaToServer(garantiaData, isFinalizar);
                });
            } else {
                saveGarantiaToServer(garantiaData, isFinalizar);
            }
        });

        function saveGarantiaToServer(garantiaData, isFinalizar) {
            $.ajax({
                url: 'save_garantia.php',
                type: 'POST',
                data: {
                    garantia_data: JSON.stringify(garantiaData),
                    action: isFinalizar ? 'finalizar' : 'gravar'
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.success) {
                        // Notificação Email (Opcional)
                        $.ajax({
                            url: 'modules/workflow/send_email_garantia.php',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                garantia_data: garantiaData
                            })
                        });

                        clearTempStorage();
                        Swal.fire({
                            title: 'Sucesso!',
                            text: res.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.href = 'index.php?page=garantias';
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro',
                            text: res.message || 'Erro ao salvar JSON.',
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Erro Crítico',
                        text: 'Não foi possível acessar o save_garantia.php na raiz.',
                        icon: 'error'
                    });
                }
            });
        }

        // ========== FINALIZAÇÃO ==========

        function checkFields() {
            $('#descCliente strong').css('color', clientInput.val() ? '#28a745' : '');
            productDescLabel.css('color', productInput.val() ? '#28a745' : '');
        }

        $('#btnExcluirCliente').on('click', function() {
            clientInput.val('').removeData('cliente-data');
            clientNameLabel.html('<strong>Nome do Cliente será mostrado aqui para confirmação</strong>');
            saveToTempStorage();
            checkFields();
        });

        $('#limparDadosTemp').on('click', function() {
            Swal.fire({
                title: 'Limpar tudo?',
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) {
                    clearTempStorage();
                    window.location.reload();
                }
            });
        });

        // Carregar dados ao iniciar
        loadFromTempStorage();

        $(window).on('beforeunload', function() {
            if (garantiaItens.length > 0) saveToTempStorage();
        });
    });
</script>