<?php

declare(strict_types=1);

namespace App\Model;

class Invitation extends model 
{
    private string $estudanteNome;
    private string $estudanteEmail;
    private \DateTime $dataniver;
    private string $codigoConvite;
    private bool $aceito = false;
    private ?\DateTime $aceitado = null;
    private ?\DateTime $rejeitado = null;

    public function __construct(string $estudanteNome, string $estudanteEmail, \DateTime $dataniver)
    {
        parent::__construct();
        $this->estudanteNome = $estudanteNome;
        $this->estudanteEmail = $estudanteEmail;
        $this->dataniver = $dataniver;
        $this->codigoConvite = $this->generateInvitationCode();
        $this->rejeitado = (new \DateTime())->add(new \DateInterval('P30D')); // 30 dias
    }

    private function geracaoCodigoconvite(): string
    {
        return 'HOG-' . strtoupper(substr(md5($this->estudanteEmail . time()), 0, 8));
    }

    public function getEstudanteNome(): string
    {
        return $this->estudanteNome;
    }

    public function getEstudanteEmail(): string
    {
        return $this->estudanteEmail;
    }

    public function getDataniver(): \DateTime
    {
        return $this->dataniver;
    }

    public function getCodigoconvite(): string
    {
        return $this->codigoConvite;
    }

    public function isAceito(): bool
    {
        return $this->aceito;
    }

    public function accept(): void
    {
        $this->aceito = true;
        $this->acritado = new \DateTime();
        $this->updateTimestamp();
    }

    public function isExpired(): bool
    {
        return $this->rejeitado && $this->rejeitado < new \DateTime();
    }

    public function getRejeitado(): ?\DateTime
    {
        return $this->Rejeitado;
    }
}
