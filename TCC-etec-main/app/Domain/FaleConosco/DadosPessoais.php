<?php

namespace App\Domain\FaleConosco;

class DadosPessoais extends Contato
{
    public function processar(): string
    {
        return sprintf(
            'Solicitação de acesso a dados pessoais processada: %s (Enviada por: %s, Email: %s)<br>Entraremos em contato em breve.',
            $this->getMensagem(),
            $this->getNome(),
            $this->getEmail()
        );
    }
}
