<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$tipoUsuario = $_SESSION['tipo'] ?? $_SESSION['user_tipo'] ?? ''; 
$permitidos = ['SAC', 'ADMIN'];

if (!in_array(strtoupper($tipoUsuario), $permitidos)) {
    die("Acesso negado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valores Aprovados | Sistema GNV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --azul-fiscal: #1e293b; --verde-recuperacao: #84cc16; }
        .bg-azul-fiscal { background-color: #1e293b; }
        .bg-verde-recuperacao { background-color: #79ac43; }
    </style>
</head>
<body class="h-full overflow-hidden">
    <div class="h-full flex flex-col p-4 lg:p-6 gap-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border shadow-sm">
            <div>
                <h2 class="text-xl font-black text-azul-fiscal uppercase tracking-tighter flex items-center">
                    <i data-lucide="check-circle" class="mr-2 text-verde-recuperacao"></i>
                    Valores de Garantia Aprovados
                </h2>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-1">Análise detalhada por fabricante e item</p>
            </div>
            <div class="flex gap-2">
                <button onclick="exportarExcel()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-azul-fiscal px-4 py-2 rounded-lg font-bold text-xs transition">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600"></i> EXPORTAR EXCEL
                </button>
                <button onclick="imprimirIframe()" class="flex items-center gap-2 bg-azul-fiscal text-white px-4 py-2 rounded-lg font-bold text-xs shadow-lg hover:scale-105 transition">
                    <i data-lucide="printer" class="w-4 h-4"></i> IMPRIMIR
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col lg:flex-row gap-6 min-h-0">
            <!-- Filtros -->
            <aside class="w-full lg:w-80 bg-white rounded-2xl border shadow-sm p-6 flex flex-col shrink-0">
                <form id="filtroForm" class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase text-gray-400 mb-4 tracking-widest">Parâmetros de Data</h4>
                    
                    <div>
                        <label class="text-[10px] font-bold text-azul-fiscal uppercase block mb-1">Data Inicial</label>
                        <input type="date" id="dataDe" class="w-full border rounded-lg px-3 py-2 text-sm bg-slate-50 outline-none focus:ring-2 ring-blue-100" value="<?= date('Y-m-01') ?>">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-azul-fiscal uppercase block mb-1">Data Final</label>
                        <input type="date" id="dataAte" class="w-full border rounded-lg px-3 py-2 text-sm bg-slate-50 outline-none focus:ring-2 ring-blue-100" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="pt-4">
                        <button type="button" id="btnConsultar" class="w-full bg-verde-recuperacao text-white font-bold py-3 rounded-xl shadow-md hover:brightness-110 transition flex items-center justify-center gap-2 uppercase text-xs tracking-widest">
                            <i data-lucide="refresh-cw" id="iconRefresh" class="w-4 h-4"></i> Gerar Relatório
                        </button>
                    </div>
                </form>
                
                <div class="mt-auto p-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <p class="text-[10px] text-slate-500 leading-relaxed">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                        Este relatório exibe apenas itens que já passaram pelo processo de aprovação final.
                    </p>
                </div>
            </aside>

            <!-- Viewer -->
            <div class="flex-1 bg-slate-100 rounded-2xl border-4 border-white shadow-inner overflow-hidden relative">
                <div id="placeholderView" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-slate-50">
                    <i data-lucide="file-text" class="w-12 h-12 mb-2 opacity-20"></i>
                    <p class="text-sm font-medium">Selecione as datas e clique em gerar</p>
                </div>
                <iframe id="pdfViewer" src="about:blank" class="w-full h-full border-none hidden"></iframe>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        function getUrlParams() {
            return new URLSearchParams({
                dataDe: document.getElementById('dataDe').value,
                dataAte: document.getElementById('dataAte').value
            });
        }

document.getElementById('btnConsultar').addEventListener('click', () => {
            const dataDe = document.getElementById('dataDe').value;
            const dataAte = document.getElementById('dataAte').value;

            if(!dataDe || !dataAte) {
                alert('Por favor, selecione o período.');
                return;
            }

            document.getElementById('iconRefresh').classList.add('animate-spin');
            const iframe = document.getElementById('pdfViewer');
            
            // AJUSTE DO CAMINHO ABAIXO:
            iframe.src = `modules/relatorios/rptValoresAprovadosImpressao.php?${getUrlParams().toString()}`;
            
            iframe.onload = () => {
                document.getElementById('iconRefresh').classList.remove('animate-spin');
                document.getElementById('placeholderView').classList.add('hidden');
                iframe.classList.remove('hidden');
            };
        });

        function exportarExcel() {
            const params = getUrlParams();
            params.append('export', 'excel');
            // AJUSTE DO CAMINHO ABAIXO:
            window.location.href = `modules/relatorios/rptValoresAprovadosImpressao.php?${params.toString()}`;
        }
        function imprimirIframe() {
            const iframe = document.getElementById('pdfViewer');
            if (iframe.src.includes('about:blank')) return alert('Gere o relatório primeiro!');
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    </script>
</body>
</html>
