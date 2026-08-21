<?php
// Simulação das variáveis de sessão (mantenha sua lógica original de buscar do banco/sessão)
$rca = $_SESSION['user_rca'] ?? 0;
$cliente = $_SESSION['user_cliente'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Garantias | Sistema GNV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
    /* Cores do seu modelo */
    :root {
        --azul-fiscal: #1e293b;
        --verde-recuperacao: #84cc16;
        --laranja-acao: #f97316;
    }

    .bg-azul-fiscal {
        background-color: #1e293b;
    }

    .text-azul-fiscal {
        color: #1e293b;
    }

    .bg-verde-recuperacao {
        background-color: #79ac43;
    }

    .accent-laranja-acao {
        accent-color: #f97316;
    }
    </style>
</head>

<body class="h-full overflow-hidden">

    <div class="h-full flex flex-col p-4 lg:p-6 gap-4">

        <!-- Header do Relatório -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border shadow-sm">
            <div>
                <h2 class="text-xl font-black text-azul-fiscal uppercase tracking-tighter flex items-center">
                    <i data-lucide="shield-check" class="mr-2 text-verde-recuperacao"></i>
                    Consulta de Garantias
                </h2>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-1">Status de solicitações e
                    protocolos</p>
            </div>
            <div class="flex gap-2">
                <button onclick="exportarPDF()"
                    class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-azul-fiscal px-4 py-2 rounded-lg font-bold text-xs transition">
                    <i data-lucide="download" class="w-4 h-4"></i> EXPORTAR PDF
                </button>
                <button onclick="imprimirIframe()"
                    class="flex items-center gap-2 bg-azul-fiscal text-white px-4 py-2 rounded-lg font-bold text-xs shadow-lg hover:scale-105 transition">
                    <i data-lucide="printer" class="w-4 h-4"></i> IMPRIMIR
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col lg:flex-row gap-6 min-h-0">

            <!-- Coluna de Filtros (Esquerda) -->
            <aside class="w-full lg:w-80 bg-white rounded-2xl border shadow-sm p-6 flex flex-col shrink-0">
                <h4 class="text-[10px] font-black uppercase text-gray-400 mb-4 tracking-[0.2em]">Parâmetros de Refino
                </h4>

                <form id="filtroForm" class="space-y-4 flex-1">
                    <!-- Cliente -->
                    <div>
                        <label class="text-[10px] font-bold text-azul-fiscal uppercase block mb-1">Código do
                            Cliente</label>
                        <input type="text" id="codigoCliente" name="codigoCliente"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 ring-blue-100 outline-none bg-slate-50"
                            placeholder="Ex: 12345" value="<?php echo ($cliente > 0) ? $cliente : ''; ?>">
                    </div>

                    <!-- RCA -->
                    <div>
                        <label class="text-[10px] font-bold text-azul-fiscal uppercase block mb-1">Código RCA
                            (Vendedor)</label>
                        <input type="text" id="codigoRCA" name="codigoRCA"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 ring-blue-100 outline-none bg-slate-50"
                            placeholder="Ex: 50" value="<?php echo ($rca > 0) ? $rca : ''; ?>">
                    </div>

                    <!-- Simulação de Status (Opcional) -->
                    <div>
                        <label class="text-[10px] font-bold text-azul-fiscal uppercase block mb-1">Status da
                            Garantia</label>
                        <div class="space-y-2 mt-2">
                            <label class="flex items-center text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" checked class="mr-2 accent-laranja-acao"> Aprovados
                            </label>
                            <label class="flex items-center text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" checked class="mr-2 accent-laranja-acao"> Pendentes
                            </label>
                        </div>
                    </div>

                    <button type="button" id="btnConsultar"
                        class="w-full bg-verde-recuperacao text-white font-bold py-3 rounded-xl shadow-md hover:brightness-110 transition flex items-center justify-center gap-2 mt-6 uppercase text-xs tracking-widest">
                        <i data-lucide="refresh-cw" id="iconRefresh" class="w-4 h-4"></i> Atualizar Relatório
                    </button>

                    <button type="button" id="btnLimpar"
                        class="w-full text-gray-400 font-bold py-2 text-[10px] uppercase tracking-widest hover:text-gray-600 transition">
                        Limpar Filtros
                    </button>
                </form>
            </aside>

            <!-- Viewer de PDF / Resultados (Direita) -->
            <div
                class="flex-1 bg-slate-200 rounded-2xl border-4 border-white shadow-inner overflow-hidden relative min-h-[400px]">

                <!-- Placeholder Inicial -->
                <div id="placeholderView"
                    class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-slate-100 z-10">
                    <i data-lucide="search" class="w-12 h-12 mb-2 opacity-20"></i>
                    <p class="text-sm font-medium">Clique em atualizar para gerar o relatório</p>
                </div>

                <!-- Iframe do Relatório -->
                <iframe id="pdfViewer" src="about:blank" class="w-full h-full border-none hidden"
                    title="Visualizador de Relatório"></iframe>

                <!-- Marca d'água -->
                <div
                    class="absolute bottom-4 right-4 bg-azul-fiscal/80 text-white text-[9px] px-3 py-1 rounded-full font-bold backdrop-blur-sm pointer-events-none">
                    SISTEMA GNV | VISUALIZADOR OFICIAL
                </div>
            </div>
        </div>
    </div>

    <script>
    // Inicializa ícones
    lucide.createIcons();

    const sessaoRca = <?php echo json_encode($rca); ?>;
    const sessaoCliente = <?php echo json_encode($cliente); ?>;

    const btnConsultar = document.getElementById('btnConsultar');
    const btnLimpar = document.getElementById('btnLimpar');
    const pdfViewer = document.getElementById('pdfViewer');
    const placeholderView = document.getElementById('placeholderView');
    const iconRefresh = document.getElementById('iconRefresh');

    btnConsultar.addEventListener('click', () => {
        if (validarPermissao()) {
            gerarRelatorio();
        }
    });

    btnLimpar.addEventListener('click', () => {
        document.getElementById('filtroForm').reset();
        pdfViewer.classList.add('hidden');
        placeholderView.classList.remove('hidden');
        pdfViewer.src = 'about:blank';
    });

    function validarPermissao() {
        const inputRca = document.getElementById('codigoRCA').value.trim();
        const inputCliente = document.getElementById('codigoCliente').value.trim();

        if (sessaoRca > 0 && inputRca !== "" && inputRca != sessaoRca) {
            alert("Atenção: Você só pode pesquisar o seu RCA (" + sessaoRca + ")");
            return false;
        }
        if (sessaoCliente > 0 && inputCliente !== "" && inputCliente != sessaoCliente) {
            alert("Atenção: Você só pode pesquisar o seu código de cliente (" + sessaoCliente + ")");
            return false;
        }
        return true;
    }

    function gerarRelatorio() {
        // Efeito de loading no ícone
        iconRefresh.classList.add('animate-spin');

        const codCli = document.getElementById('codigoCliente').value;
        const codRca = document.getElementById('codigoRCA').value;

        // Construímos a URL para um novo arquivo que criaremos (rptGarListaImpressao.php)
        // Esse arquivo vai ler os GETs e mostrar a tabela formatada para impressão
        const url = `modules/relatorios/rptGarListaImpressao.php?codigoCliente=${codCli}&codigoRCA=${codRca}`;

        pdfViewer.src = url;

        pdfViewer.onload = () => {
            iconRefresh.classList.remove('animate-spin');
            placeholderView.classList.add('hidden');
            pdfViewer.classList.remove('hidden');
        };
    }

    function imprimirIframe() {
        const iframe = document.getElementById('pdfViewer');
        if (iframe.src === 'about:blank') return alert('Gere o relatório primeiro!');
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }

    function exportarPDF() {
        // Como estamos usando HTML no iframe, a melhor forma de "Exportar PDF" 
        // em sistemas web simples é disparar o comando de impressão que permite "Salvar como PDF"
        imprimirIframe();
    }
    </script>
</body>

</html>