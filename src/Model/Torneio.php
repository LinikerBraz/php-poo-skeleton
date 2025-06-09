<?php

namespace Hogwarts\Models\Module3;

use Hogwarts\Models\Base\Entity;

class Tournament extends Entity
{
    private string $nome;
    private string $descricao;
    private \DateTime $dataInicio;
    private \DateTime $dataFim;
    private array $participantes = [];
    private array $competicoes = [];
    private bool $ativo = false;
    private ?int $idVencedor = null;
    private array $regras = [];
    private ?string $organizador = null;
    private int $idadeMinima = 0;
    private array $premios = [];
    private array $patrocinadores = [];

    public function __construct(string $nome, string $descricao, \DateTime $dataInicio, \DateTime $dataFim)
    {
        parent::__construct();
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
        $this->atualizarTimestamp();
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
        $this->atualizarTimestamp();
    }

    public function getDataInicio(): \DateTime
    {
        return $this->dataInicio;
    }

    public function setDataInicio(\DateTime $dataInicio): void
    {
        $this->dataInicio = $dataInicio;
        $this->atualizarTimestamp();
    }

    public function getDataFim(): \DateTime
    {
        return $this->dataFim;
    }

    public function setDataFim(\DateTime $dataFim): void
    {
        $this->dataFim = $dataFim;
        $this->atualizarTimestamp();
    }

    public function getParticipantes(): array
    {
        return $this->participantes;
    }

    public function adicionarParticipante(int $idAluno): void
    {
        if (!in_array($idAluno, $this->participantes)) {
            $this->participantes[] = $idAluno;
            $this->atualizarTimestamp();
        }
    }

    public function removerParticipante(int $idAluno): void
    {
        $chave = array_search($idAluno, $this->participantes);
        if ($chave !== false) {
            unset($this->participantes[$chave]);
            $this->participantes = array_values($this->participantes);
            $this->atualizarTimestamp();
        }
    }

    public function getCompeticoes(): array
    {
        return $this->competicoes;
    }

    public function adicionarCompeticao(Competition $competicao): void
    {
        $this->competicoes[] = $competicao;
        $this->atualizarTimestamp();
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function iniciar(): void
    {
        $this->ativo = true;
        $this->atualizarTimestamp();
    }

    public function finalizar(): void
    {
        $this->ativo = false;
        $this->atualizarTimestamp();
    }

    public function getIdVencedor(): ?int
    {
        return $this->idVencedor;
    }

    public function setVencedor(int $idVencedor): void
    {
        $this->idVencedor = $idVencedor;
        $this->atualizarTimestamp();
    }

    public function getRegras(): array
    {
        return $this->regras;
    }

    public function adicionarRegra(string $regra): void
    {
        $this->regras[] = $regra;
        $this->atualizarTimestamp();
    }

    public function removerRegra(int $indice): bool
    {
        if (isset($this->regras[$indice])) {
            unset($this->regras[$indice]);
            $this->regras = array_values($this->regras);
            $this->atualizarTimestamp();
            return true;
        }
        return false;
    }

    public function getOrganizador(): ?string
    {
        return $this->organizador;
    }

    public function setOrganizador(string $organizador): void
    {
        $this->organizador = $organizador;
        $this->atualizarTimestamp();
    }

    public function getIdadeMinima(): int
    {
        return $this->idadeMinima;
    }

    public function setIdadeMinima(int $idadeMinima): void
    {
        if ($idadeMinima < 0) {
            throw new \InvalidArgumentException("Idade mínima não pode ser negativa");
        }
        $this->idadeMinima = $idadeMinima;
        $this->atualizarTimestamp();
    }

    public function getPremios(): array
    {
        return $this->premios;
    }

    public function adicionarPremio(string $descricao, int $posicao, string $valor = null): void
    {
        $this->premios[] = [
            'descricao' => $descricao,
            'posicao' => $posicao,
            'valor' => $valor
        ];
        $this->atualizarTimestamp();
    }

    public function getPatrocinadores(): array
    {
        return $this->patrocinadores;
    }

    public function adicionarPatrocinador(string $nome, string $tipo = 'Padrão'): void
    {
        $this->patrocinadores[] = [
            'nome' => $nome,
            'tipo' => $tipo,
            'dataAdicao' => new \DateTime()
        ];
        $this->atualizarTimestamp();
    }

    public function getQuantidadeParticipantes(): int
    {
        return count($this->participantes);
    }

    public function getQuantidadeCompeticoes(): int
    {
        return count($this->competicoes);
    }

    public function getStatus(): string
    {
        $agora = new \DateTime();
        
        if ($this->ativo) {
            return 'Em andamento';
        } elseif ($agora < $this->dataInicio) {
            return 'Agendado';
        } elseif ($agora > $this->dataFim) {
            return 'Finalizado';
        } else {
            return 'Não iniciado';
        }
    }

    public function getDuracao(): int
    {
        $diferenca = $this->dataInicio->diff($this->dataFim);
        return $diferenca->days + 1; // Incluindo o dia de início
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'dataInicio' => $this->dataInicio->format('Y-m-d'),
            'dataFim' => $this->dataFim->format('Y-m-d'),
            'status' => $this->getStatus(),
            'duracao' => $this->getDuracao(),
            'participantes' => $this->participantes,
            'quantidadeParticipantes' => $this->getQuantidadeParticipantes(),
            'competicoes' => array_map(function($competicao) {
                return $competicao->getId();
            }, $this->competicoes),
            'quantidadeCompeticoes' => $this->getQuantidadeCompeticoes(),
            'ativo' => $this->ativo,
            'idVencedor' => $this->idVencedor,
            'regras' => $this->regras,
            'organizador' => $this->organizador,
            'idadeMinima' => $this->idadeMinima,
            'premios' => $this->premios,
            'patrocinadores' => $this->patrocinadores,
            'criadoEm' => $this->getCriadoEm()->format('Y-m-d H:i:s'),
            'atualizadoEm' => $this->getAtualizadoEm()->format('Y-m-d H:i:s')
        ];
    }
}
