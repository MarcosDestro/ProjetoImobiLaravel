$(function(){
    /** Setup do header do ajax enviando o csrf token */
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /** Selecione o form de nome login */
    $('form[name="login"]').submit(function(event){
        event.preventDefault(); // Previne o refresh

        const form = $(this); // Este elemento
        const action = form.attr('action'); // Captura atributo action
        const email = form.find('input[name="email"]').val(); // Valor input name
        const password = form.find('input[name="password_check"]').val(); // Valor input password

        /**
         * É necessário passar 4 parâmetros, sendo:
         * 1° Pra onde será disparado, 2° Dados com chave valor, 3° função callback, 4° tipo de retorno
         */
        $.post(action, {email: email, password: password}, function(response){
            
            /** Se houver qualquer mensagem exiba */
            if(response.message){
                ajaxMessage(response.message, 3);
            }

            /** Se houver um redirecionamento... */
            if(response.redirect){
                window.location.href = response.redirect;
            }
            
        }, 'json');

        /** Teste de dados */
        // console.log(action, email, password);
    });

    // AJAX RESPONSE
    var ajaxResponseBaseTime = 3;

    function ajaxMessage(message, time) {
        var ajaxMessage = $(message);

        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({"width": "100%"}, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
    }

    // AJAX RESPONSE MONITOR
    $(".ajax_response .message").each(function (e, m) {
        ajaxMessage(m, ajaxResponseBaseTime += 1);
    });

    // AJAX MESSAGE CLOSE ON CLICK
    $(".ajax_response").on("click", ".message", function (e) {
        $(this).effect("bounce").fadeOut(1);
    });
   
});

