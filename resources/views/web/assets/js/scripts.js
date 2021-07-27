$(function () {

    /** Tradução dos campos */
    function translateField(fieldValue){

        switch (fieldValue) {
            case 'home':
                value = 'Casa';
                break;
            case 'roof':
                value = 'Cobertura';
                break;
            case 'apartment':
                value = 'Apartamento';
                break;
            case 'studio':
                value = 'Studio';
                break;
            case 'kitnet':
                value = 'Kitnet';
                break;
            case 'commercial_room':
                value = 'Sala Comercial';
                break;
            case 'deposit_shed':
                value = 'Depósito/Galpão';
                break;
            case 'commercial_point':
                value = 'Ponto Comercial';
                break;
            case 'terrain':
                value = 'Terreno';
                break;
            case 'residential_property':
                value = 'Imóvel Residencial';
                break;
            case 'commercial_industrial':
                value = 'Comercial/Industrial';
                break;
            // Padrão
            default:
                value = fieldValue;
                break;
            } 
        return value;
    }

    $('body').on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $('.open_filter').on('click', function (event) {
        event.preventDefault();

        box = $(".form_advanced");
        button = $(this);

        if (box.css("display") !== "none") {
            button.text("Filtro Avançado ↓");
        } else {
            button.text("✗ Fechar");
        }

        box.slideToggle();
    });

    /** Monitorar o body, sendo change em qualquer elemento que possua o filter_ antes */
    $('body').on('change', 'select[name*="filter_"]', function(){

        /** Setup do header do ajax enviando o csrf token */
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var search = $(this);
        var nextIndex = $(this).data('index') + 1;

        /** Post para data-action, enviando valor de search */
        $.post(search.data('action'), {search: search.val()}, function(response){
            // Se a resposta foi de sucesso...
            if(response.status === 'success'){
                // Esvazie todos os próximos selects
                $('select[data-index="' + nextIndex + '"]').empty();

                // Preenche o próximo input
                $.each(response.data, function(key, value){
                    $('select[data-index="' + nextIndex + '"]').append(
                        $('<option>', {
                            value: value,
                            text: translateField(value)
                        })
                    );
                })

                // Zerar os próximos inputs
                $.each($('select[name*="filter_"]'), function(index, element){
                    if($(element).data('index') >= (nextIndex + 1)){
                        $(element).empty().append(
                            // Seta um option padrão com disabled
                            $('<option>', {
                                text: 'Selecione a opção anterior',
                                disabled: true
                            })
                        );
                    }
                });

                // Atualize o componente selectpicker
                $('.selectpicker').selectpicker('refresh');

            }

            // Caso tenha uma falha
            if(response.status == 'fail'){
                // Zerar os inputs
                $.each($('select[name*="filter_"]'), function(index, element){
                    if($(element).data('index') >= nextIndex ){
                        $(element).empty().append(
                            // Seta um option padrão com disabled
                            $('<option>', {
                                text: 'Selecione a opção anterior',
                                disabled: true
                            })
                        );
                    }
                });
            }

        }, 'json');
    });

    // // Limpa o csrf do cabeçalho
    // delete $.ajaxSettings.headers['X-CSRF-TOKEN'];

});