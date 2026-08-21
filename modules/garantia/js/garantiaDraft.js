/**
 * Script de Persistência de Rascunho para Garantias
 */

// Identificador único persistente para o usuário logado (passado via PHP no arquivo principal)
// Se preferir, você pode definir essa var no garantiaAdd.php antes de chamar este script
const userDraftId = "<?php echo $codusur1 ? $codusur1 : $cliente; ?>";

function saveToTempStorage() {
    // Pega os dados atuais das globais do script principal
    const codCliVal = $('#codCliente').val().trim();
    if (window.garantiaItens.length === 0 && codCliVal === '') return;

    const tempData = {
        cliente: {
            codCliente: codCliVal,
            clienteData: $('#codCliente').data('cliente-data'),
            descCliente: $('#descCliente').text()
        },
        garantiaItens: window.garantiaItens,
        currentAnexoId: window.currentAnexoId
    };

    $.ajax({
        url: 'save_temp_garantia.php',
        type: 'POST',
        data: { user_id: userDraftId, temp_data: JSON.stringify(tempData) },
        dataType: 'json',
        success: function () { console.log("Rascunho atualizado no servidor."); }
    });
}

function loadFromTempStorage() {
    $.ajax({
        url: 'load_temp_garantia.php',
        type: 'GET',
        data: { user_id: userDraftId },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.temp_data) {
                const draft = JSON.parse(response.temp_data);

                // Restaura Cliente
                if (draft.cliente && draft.cliente.codCliente) {
                    $('#codCliente').val(draft.cliente.codCliente);
                    $('#codCliente').data('cliente-data', draft.cliente.clienteData);
                    $('#descCliente').html('<strong>' + draft.cliente.descCliente + '</strong>');
                }

                // Restaura Itens
                if (draft.garantiaItens && draft.garantiaItens.length > 0) {
                    window.garantiaItens = draft.garantiaItens;
                    window.currentAnexoId = draft.currentAnexoId || 0;

                    // Chama função de renderizar tabela do script principal
                    if (typeof updateItensTable === 'function') updateItensTable();

                    Swal.fire({
                        title: 'Rascunho Encontrado',
                        text: 'Deseja recuperar os itens da última sessão?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, recuperar',
                        cancelButtonText: 'Não, limpar'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            clearTempStorage();
                            location.reload();
                        }
                    });
                }
            }
        }
    });
}

function clearTempStorage() {
    $(window).off('beforeunload');
    $.ajax({
        url: 'clear_temp_garantia.php',
        type: 'POST',
        data: { user_id: userDraftId }
    });
}

// Inicialização
$(document).ready(function () {
    loadFromTempStorage();

    // Salva automaticamente se fechar a aba
    $(window).on('beforeunload', function () {
        saveToTempStorage();
    });
});