<?php

namespace App\Domain\FaleConosco;

class ProblemaComLogin extends Contato
{
    public function processar(): string
    {
        return sprintf(
            'Obrigado por relatar seu problema de login, <strong>%s</strong>! Nossa equipe entrará em contato em breve para ajudar a resolver o problema.',
            $this->getNome()
        );
    }
}
