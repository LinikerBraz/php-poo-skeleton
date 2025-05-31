<?php

namespace Hogwarts\Models\Module1;

use Hogwarts\Models\Base\Entity;

class Invitation extends Entity
{
    private string $nomeAluno;
    private string $emailAluno;
    private \DateTime $dataNascimento;
    private string $codigoConvite;
    private bool $aceito = false;
    private ?\DateTime $aceitoEm = null;
    private ?\DateTime $expiraEm = null;
    private array $habilidadesMagicas = [];
    private ?string $antecedentesFamiliares = null;
    private int $contadorTentativas = 0;
    private int $maximoTentativas = 3;
    private bool $cancelado = false;
    private ?\DateTime $canceladoEm = null;
    private ?string $motivoCancelamento = null;
    private array $observacoesAdicionais = [];

    public function __construct(string $nomeAluno, string $emailAluno, \DateTime $dataNascimento)
    {
        parent::__construct();
        $this->nomeAluno = $nomeAluno;
        $this->emailAluno = $emailAluno;
        $this->dataNascimento = $dataNascimento;
        $this->codigoConvite = $this->gerarCodigoConvite();
        $this->expiraEm = (new \DateTime())->add(new \DateInterval('P30D')); // 30 dias
    }

    private function gerarCodigoConvite(): string
    {
        $iniciaisNome = strtoupper(substr($this->nomeAluno, 0, 2));
        $hashUnico = strtoupper(substr(md5($this->emailAluno . time()), 0, 6));
        return 'HOG-' . $iniciaisNome . '-' . $hashUnico;
    }

    public function getNomeAluno(): string
    {
        return $this->nomeAluno;
    }

    public function getEmailAluno(): string
    {
        return $this->emailAluno;
    }

    public function getDataNascimento(): \DateTime
    {
        return $this->dataNascimento;
    }

    public function getCodigoConvite(): string
    {
        return $this->codigoConvite;
    }

    public function isAceito(): bool
    {
        return $this->aceito;
    }

    public function aceitar(): void
    {
        if ($this->isExpirado()) {
            throw new \RuntimeException("Convite expirado");
        }
        
        if ($this->cancelado) {
            throw new \RuntimeException("Convite cancelado");
        }
        
        $this->aceito = true;
        $this->aceitoEm = new \DateTime();
        $this->atualizarTimestamp();
    }

    public function isExpirado(): bool
    {
        return $this->expiraEm && $this->expiraEm < new \DateTime();
    }

    public function getExpiraEm(): ?\DateTime
    {
        return $this->expiraEm;
    }

    public function estenderExpiracao(int $dias): void
    {
        if ($this->aceito || $this->cancelado) {
            throw new \RuntimeException("Não é possível estender um convite já aceito ou cancelado");
        }
        
        $this->expiraEm = (new \DateTime())->add(new \DateInterval("P{$dias}D"));
        $this->atualizarTimestamp();
    }

    public function getAceitoEm(): ?\DateTime
    {
        return $this->aceitoEm;
    }

    public function getHabilidadesMagicas(): array
    {
        return $this->habilidadesMagicas;
    }

    public function adicionarHabilidadeMagica(string $habilidade): void
    {
        if (!in_array($habilidade, $this->habilidadesMagicas)) {
            $this->habilidadesMagicas[] = $habilidade;
            $this->atualizarTimestamp();
        }
    }

    public function getAntecedentesFamiliares(): ?string
    {
        return $this->antecedentesFamiliares;
    }

    public function setAntecedentesFamiliares(string $antecedentes): void
    {
        $this->antecedentesFamiliares = $antecedentes;
        $this->atualizarTimestamp();
    }

    public function getContadorTentativas(): int
    {
        return $this->contadorTentativas;
    }

    public function incrementarTentativa(): bool
    {
        if ($this->contadorTentativas >= $this->maximoTentativas) {
            return false;
        }
        
        $this->contadorTentativas++;
        $this->atualizarTimestamp();
        return true;
    }

    public function getMaximoTentativas(): int
    {
        return $this->maximoTentativas;
    }

    public function setMaximoTentativas(int $maximoTentativas): void
    {
        if ($maximoTentativas < 1) {
            throw new \InvalidArgumentException("Número máximo de tentativas deve ser pelo menos 1");
        }
        
        $this->maximoTentativas = $maximoTentativas;
        $this->atualizarTimestamp();
    }

    public function isCancelado(): bool
    {
        return $this->cancelado;
    }

    public function cancelar(string $motivo = null): void
    {
        if ($this->aceito) {
            throw new \RuntimeException("Não é possível cancelar um convite já aceito");
        }
        
        $this->cancelado = true;
        $this->canceladoEm = new \DateTime();
        $this->motivoCancelamento = $motivo;
        $this->atualizarTimestamp();
    }

    public function getCanceladoEm(): ?\DateTime
    {
        return $this->canceladoEm;
    }

    public function getMotivoCancelamento(): ?string
    {
        return $this->motivoCancelamento;
    }

    public function getObservacoesAdicionais(): array
    {
        return $this->observacoesAdicionais;
    }

    public function adicionarObservacao(string $observacao): void
    {
        $this->observacoesAdicionais[] = [
            'observacao' => $observacao,
            'data' => new \DateTime()
        ];
        $this->atualizarTimestamp();
    }

    public function isValido(): bool
    {
        return !$this->isExpirado() && !$this->isCancelado() && !$this->isAceito();
    }

    public function getStatus(): string
    {
        if ($this->aceito) {
            return 'Aceito';
        } elseif ($this->cancelado) {
            return 'Cancelado';
        } elseif ($this->isExpirado()) {
            return 'Expirado';
        } else {
            return 'Pendente';
        }
    }

    public function getDiasParaExpirar(): int
    {
        if (!$this->expiraEm || $this->isExpirado()) {
            return 0;
        }
        
        $agora = new \DateTime();
        $diferenca = $agora->diff($this->expiraEm);
        return $diferenca->days;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nomeAluno' => $this->nomeAluno,
            'emailAluno' => $this->emailAluno,
            'dataNascimento' => $this->dataNascimento->format('Y-m-d'),
            'codigoConvite' => $this->codigoConvite,
            'status' => $this->getStatus(),
            'expiraEm' => $this->expiraEm ? $this->expiraEm->format('Y-m-d H:i:s') : null,
            'aceitoEm' => $this->aceitoEm ? $this->aceitoEm->format('Y-m-d H:i:s') : null,
            'habilidadesMagicas' => $this->habilidadesMagicas,
            'antecedentesFamiliares' => $this->antecedentesFamiliares,
            'contadorTentativas' => $this->contadorTentativas,
            'maximoTentativas' => $this->maximoTentativas,
            'diasParaExpirar' => $this->getDiasParaExpirar(),
            'criadoEm' => $this->getCriadoEm()->format('Y-m-d H:i:s'),
            'atualizadoEm' => $this->getAtualizadoEm()->format('Y-m-d H:i:s')
        ];
    }
}
