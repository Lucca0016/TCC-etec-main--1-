<?php

namespace App\Domain\FaleConosco;

class Feedback extends Contato
{
    /** @var string */
    private $tipo;

    public function __construct(string $nome, string $email, string $mensagem, string $tipo)
    {
        parent::__construct($nome, $email, $mensagem);
        $this->tipo = $tipo;
    }

    public function processar(): string
    {
        return sprintf(
            'Obrigado por seu feedback, %s! Tipo: %s<br>Mensagem: %s',
            $this->getNome(),
            $this->tipo,
            $this->getMensagem()
        );
    }
}
