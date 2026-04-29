<?php

namespace App\Domain\FaleConosco;

abstract class Contato
{
    protected string $nome;
    protected string $email;
    protected string $mensagem;

    public function __construct(string $nome, string $email, string $mensagem)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->mensagem = $mensagem;
    }

    abstract public function processar(): string;

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getMensagem(): string
    {
        return $this->mensagem;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
