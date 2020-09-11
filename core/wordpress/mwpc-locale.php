<?php

class MWPCLocale {
    public static $message_map = [
        'deleted_records'=>'Registros apagados: %d',
        'add_new'=>"Adicionar novo",
        'teacher'=>"Professor",
        'name'=>'Nome',
        'cpf'=>'CPF',
        'email'=>'E-mail',
        'type'=>'Tipo',
        'id'=>'Código',
        'back'=>'Voltar',
        'save'=>'Salvar',
        'egress'=>'Egresso',
        'coautor'=>'Co-Autor',
        'graduate'=>'Graduação',
        'mastering'=>'Mestrado',
        'phd'=>'Doutorado',
        'save_changes'=>'Salvar alterações',
        'invalid_cpf'=>'CPF inválido!',
        'invalid_email'=>'E-mail inválido!',
        'search'=>'Buscar',
        'year'=>'Ano',
        'delete'=>'Apagar',
    ];
    public static function get($key) {
        if (!array_key_exists($key, MWPCLocale::$message_map)) CoreUtils::log("Invalid key -> ". $key);
        else return MWPCLocale::$message_map[$key];
        return '';        
    }
};