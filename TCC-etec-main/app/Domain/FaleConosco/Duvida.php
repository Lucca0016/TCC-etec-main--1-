<?php

namespace App\Domain\FaleConosco;

class Duvida extends Contato
{
    public function processar(): string
    {
        return sprintf(
            'Dúvida processada: %s (Enviada por: %s, Email: %s)<br>Entraremos em contato em breve para esclarecer sua dúvida.',
            $this->getMensagem(),
            $this->getNome(),
            $this->getEmail()
        );
    }
}
