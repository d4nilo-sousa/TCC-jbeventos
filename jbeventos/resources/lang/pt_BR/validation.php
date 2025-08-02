<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Linhas de idioma para validação
    |--------------------------------------------------------------------------
    |
    | As linhas de idioma abaixo contêm as mensagens de erro padrão usadas pela
    | classe validadora. Algumas regras possuem versões múltiplas, como as de tamanho.
    | Fique à vontade para ajustar cada mensagem aqui.
    |
    */

    'accepted'             => 'O campo :attribute deve ser aceito.',
    'accepted_if'          => 'O campo :attribute deve ser aceito quando :other for :value.',
    'active_url'           => 'O campo :attribute não é uma URL válida.',
    'after'                => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal'       => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha'                => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash'           => 'O campo :attribute deve conter apenas letras, números, traços e underlines.',
    'alpha_num'            => 'O campo :attribute deve conter apenas letras e números.',
    'array'                => 'O campo :attribute deve ser um array.',
    'ascii'                => 'O campo :attribute deve conter apenas caracteres alfanuméricos e símbolos de byte único.',
    'before'               => 'O campo :attribute deve ser uma data anterior a :date.',
    'before_or_equal'      => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
    'between'              => [
        'array'   => 'O campo :attribute deve conter entre :min e :max itens.',
        'file'    => 'O arquivo :attribute deve ter entre :min e :max kilobytes.',
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'string'  => 'O campo :attribute deve ter entre :min e :max caracteres.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'A confirmação do campo :attribute não confere.',
    'current_password'     => 'A senha está incorreta.',
    'date'                 => 'O campo :attribute não é uma data válida.',
    'date_equals'          => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format'          => 'O campo :attribute não corresponde ao formato :format.',
    'decimal'              => 'O campo :attribute deve ter :decimal casas decimais.',
    'declined'             => 'O campo :attribute deve ser recusado.',
    'declined_if'          => 'O campo :attribute deve ser recusado quando :other for :value.',
    'different'            => 'Os campos :attribute e :other devem ser diferentes.',
    'digits'               => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between'       => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions'           => 'O campo :attribute tem dimensões de imagem inválidas.',
    'distinct'             => 'O campo :attribute tem um valor duplicado.',
    'doesnt_end_with'      => 'O campo :attribute não pode terminar com um dos seguintes valores: :values.',
    'doesnt_start_with'    => 'O campo :attribute não pode começar com um dos seguintes valores: :values.',
    'email'                => 'O campo :attribute deve ser um endereço de email válido.',
    'ends_with'            => 'O campo :attribute deve terminar com um dos seguintes valores: :values.',
    'enum'                 => 'O valor selecionado para :attribute é inválido.',
    'exists'               => 'O valor selecionado para :attribute é inválido.',
    'file'                 => 'O campo :attribute deve ser um arquivo.',
    'filled'               => 'O campo :attribute deve ter um valor.',
    'gt'                   => [
        'array'   => 'O campo :attribute deve ter mais que :value itens.',
        'file'    => 'O arquivo :attribute deve ser maior que :value kilobytes.',
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'string'  => 'O campo :attribute deve ter mais que :value caracteres.',
    ],
    'gte'                  => [
        'array'   => 'O campo :attribute deve ter :value itens ou mais.',
        'file'    => 'O arquivo :attribute deve ser maior ou igual a :value kilobytes.',
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'string'  => 'O campo :attribute deve ter :value caracteres ou mais.',
    ],
    'image'                => 'O campo :attribute deve ser uma imagem.',
    'in'                   => 'O valor selecionado para :attribute é inválido.',
    'in_array'             => 'O campo :attribute não existe em :other.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'ip'                   => 'O campo :attribute deve ser um endereço IP válido.',
    'ipv4'                 => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6'                 => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json'                 => 'O campo :attribute deve ser uma string JSON válida.',
    'lt'                   => [
        'array'   => 'O campo :attribute deve ter menos que :value itens.',
        'file'    => 'O arquivo :attribute deve ser menor que :value kilobytes.',
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'string'  => 'O campo :attribute deve ter menos que :value caracteres.',
    ],
    'lte'                  => [
        'array'   => 'O campo :attribute não deve ter mais que :value itens.',
        'file'    => 'O arquivo :attribute deve ser menor ou igual a :value kilobytes.',
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'string'  => 'O campo :attribute deve ter menos ou igual a :value caracteres.',
    ],
    'max'                  => [
        'array'   => 'O campo :attribute não deve ter mais que :max itens.',
        'file'    => 'O arquivo :attribute não deve ser maior que :max kilobytes.',
        'numeric' => 'O campo :attribute não deve ser maior que :max.',
        'string'  => 'O campo :attribute não deve ter mais que :max caracteres.',
    ],
    'mimes'                => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes'            => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min'                  => [
        'array'   => 'O campo :attribute deve ter ao menos :min itens.',
        'file'    => 'O arquivo :attribute deve ter ao menos :min kilobytes.',
        'numeric' => 'O campo :attribute deve ser ao menos :min.',
        'string'  => 'O campo :attribute deve ter ao menos :min caracteres.',
    ],
    'not_in'               => 'O valor selecionado para :attribute é inválido.',
    'not_regex'            => 'O formato do campo :attribute é inválido.',
    'numeric'              => 'O campo :attribute deve ser um número.',
    'password'             => [
        'letters'       => 'O campo :attribute deve conter ao menos uma letra.',
        'mixed'         => 'O campo :attribute deve conter ao menos uma letra maiúscula e uma minúscula.',
        'numbers'       => 'O campo :attribute deve conter ao menos um número.',
        'symbols'       => 'O campo :attribute deve conter ao menos um símbolo.',
        'uncompromised' => 'O :attribute fornecido apareceu em um vazamento de dados. Por favor, escolha outro.',
    ],
    'present'              => 'O campo :attribute deve estar presente.',
    'prohibited'           => 'O campo :attribute é proibido.',
    'prohibited_if'        => 'O campo :attribute é proibido quando :other é :value.',
    'regex'                => 'O formato do campo :attribute é inválido.',
    'required'             => 'O campo :attribute é obrigatório.',
    'required_if'          => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_unless'      => 'O campo :attribute é obrigatório, exceto quando :other é :values.',
    'same'                 => 'O campo :attribute e :other devem ser iguais.',
    'size'                 => [
        'array'   => 'O campo :attribute deve conter :size itens.',
        'file'    => 'O arquivo :attribute deve ter :size kilobytes.',
        'numeric' => 'O campo :attribute deve ser :size.',
        'string'  => 'O campo :attribute deve ter :size caracteres.',
    ],
    'starts_with'          => 'O campo :attribute deve começar com um dos seguintes valores: :values.',
    'string'               => 'O campo :attribute deve ser uma string.',
    'timezone'             => 'O campo :attribute deve ser um fuso horário válido.',
    'unique'               => 'O valor do campo :attribute já está em uso.',
    'uploaded'             => 'Falha ao fazer upload do arquivo :attribute.',
    'url'                  => 'O formato do campo :attribute é inválido.',
    'uuid'                 => 'O campo :attribute deve ser um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensagens de validação personalizadas
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar mensagens de validação personalizadas para
    | atributos usando a convenção "attribute.rule" para nomear as linhas.
    | Isso facilita a especificação de mensagens específicas para uma regra.
    |
    */

    'custom' => [
        'password' => [
            'required' => 'Por favor, informe a senha.',
            'confirmed' => 'As senhas não coincidem.',
            'regex' => 'O formato da senha é inválido.',
            'min' => 'O formato da senha é inválido.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos personalizados
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas são usadas para trocar o placeholder do atributo
    | por algo mais amigável, como "E-Mail" ao invés de "email".
    |
    */

    'attributes' => [
        'name' => 'nome',
        'email' => 'email',
        'password' => 'senha',
        'password_confirmation' => 'confirmação da senha',
        'phone_number' => 'telefone',
    ],

];
