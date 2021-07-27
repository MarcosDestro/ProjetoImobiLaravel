<?php

if(! function_exists('isActive')){

    function isActive($href, $class = 'active')
    {
        /**
         *  Se tiver algum fragmento da rota em $href sendo igual a 0, ou seja, que encontrou, 
         *  devolvemos a prória classe, caso não, devolve vazio
         */
        return $class = (strpos(Route::currentRouteName(), $href) === 0 ? $class : '' );
    }
}

if(! function_exists('transFields')){

    function transField($field)
    {
        switch ($field) {
            case 'home':
                $value = 'Casa';
                break;
            case 'roof':
                $value = 'Cobertura';
                break;
            case 'apartment':
                $value = 'Apartamento';
                break;
            case 'studio':
                $value = 'Studio';
                break;
            case 'kitnet':
                $value = 'Kitnet';
                break;
            case 'commercial_room':
                $value = 'Sala Comercial';
                break;
            case 'deposit_shed':
                $value = 'Depósito/Galpão';
                break;
            case 'commercial_point':
                $value = 'Ponto Comercial';
                break;
            case 'terrain':
                $value = 'Terreno';
                break;
            case 'residential_property':
                $value = 'Imóvel Residencial';
                break;
            case 'commercial_industrial':
                $value = 'Comercial/Industrial';
                break;
            // Padrão
            default:
                $value = $field;
                break;
            } 
        return $value;
    }
}