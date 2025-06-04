<?php

namespace Hogwarts\Models\Module3;

use Hogwarts\Models\Base\Entity;

class Competition extends Entity
{
    private string $nome;
    private string $tipo;
    private \DateTime $data;
    private array $participantes = [];
    private array $resultados = [];
    private bool $finalizada = false;
    private ?string $local = null;
    private ?string $descricao = null;
    private ?int $idTorneio = null;
    private array $juizes = [];
    private int $pontuacaoMaxima = 100;
    private array $criteriosAvaliacao = [];

    public function __construct(string $nome, string $tipo, \DateTime $data)
    {
        parent::__construct();
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->data = $data;
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

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
        $this->atualizarTimestamp();
    }

    public function getData(): \DateTime
    {
        return $this->data;
    }

    public function setData(\DateTime $data): void
    {
        $this->data = $data;
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

    public function getResultados(): array
    {
        return $this->resultados;
    }

    public function adicionarResultado(int $idAluno, int $pontuacao, int $posicao): void
    {
        $this->resultados[] = [
            'idAluno' => $idAluno,
            'pontuacao' => $pontuacao,
            'posicao' => $posicao,
            'timestamp' => new \DateTime()
        ];
        $this->atualizarTimestamp();
    }

    public function isFinalizada(): bool
    {
        return $this->finalizada;
    }

    public function finalizar(): void
    {
        $this->finalizada = true;
        $this->atualizarTimestamp();
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(string $local): void
    {
        $this->local = $local;
        $this->atualizarTimestamp();
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
        $this->atualizarTimestamp();
    }

    public function getIdTorneio(): ?int
    {
        return $this->idTorneio;
    }

    public function setIdTorneio(int $idTorneio): void
    {
        $this->idTorneio = $idTorneio;
        $this->atualizarTimestamp();
    }

    public function getJuizes(): array
    {
        return $this->juizes;
    }

    public function adicionarJuiz(int $idProfessor, string $especialidade = null): void
    {
        $this->juizes[] = [
            'idProfessor' => $idProfessor,
            'especialidade' => $especialidade
        ];
        $this->atualizarTimestamp();
    }

    public function removerJuiz(int $idProfessor): bool
    {
        foreach ($this->juizes as $chave => $juiz) {
            if ($juiz['idProfessor'] === $idProfessor) {
                unset($this->juizes[$chave]);
                $this->juizes = array_values($this->juizes);
                $this->atualizarTimestamp();
                return true;
            }
        }
        return false;
    }

    public function getPontuacaoMaxima(): int
    {
        return $this->pontuacaoMaxima;
    }

    public function setPontuacaoMaxima(int $pontuacaoMaxima): void
    {
        if ($pontuacaoMaxima <= 0) {
            throw new \InvalidArgumentException("Pontuação máxima deve ser positiva");
        }
        $this->pontuacaoMaxima = $pontuacaoMaxima;
        $this->atualizarTimestamp();
    }

    public function getCriteriosAvaliacao(): array
    {
        return $this->criteriosAvaliacao;
    }

    public function adicionarCriterioAvaliacao(string $criterio, int $peso): void
    {
        if ($peso <= 0) {
            throw new \InvalidArgumentException("Peso do critério deve ser positivo");
        }
        
        $this->criteriosAvaliacao[] = [
            'criterio' => $criterio,
            'peso' => $peso
        ];
        $this->atualizarTimestamp();
    }

    public function removerCriterioAvaliacao(int $indice): bool
    {
        if (isset($this->criteriosAvaliacao[$indice])) {
            unset($this->criteriosAvaliacao[$indice]);
            $this->criteriosAvaliacao = array_values($this->criteriosAvaliacao);
            $this->atualizarTimestamp();
            return true;
        }
        return false;
    }

    public function getVencedor(): ?array
    {
        if (empty($this->resultados)) {
            return null;
        }

        usort($this->resultados, fn($a, $b) => $a['posicao'] <=> $b['posicao']);
        return $this->resultados[0] ?? null;
    }

    public function getQuantidadeParticipantes(): int
    {
        return count($this->participantes);
    }

    public function getQuantidadeJuizes(): int
    {
        return count($this->juizes);
    }

    public function getStatus(): string
    {
        $agora = new \DateTime();
        
        if ($this->finalizada) {
            return 'Finalizada';
        } elseif ($agora > $this->data) {
            return 'Em andamento';
        } else {
            return 'Agendada';
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->nome,
            'tipo' => $this->tipo,
            'data' => $this->data->format('Y-m-d H:i:s'),
            'status' => $this->getStatus(),
            'local' => $this->local,
            'descricao' => $this->descricao,
            'idTorneio' => $this->idTorneio,
            'participantes' => $this->participantes,
            'quantidadeParticipantes' => $this->getQuantidadeParticipantes(),
            'resultados' => $this->resultados,
            'finalizada' => $this->finalizada,
            'juizes' => $this->juizes,
            'quantidadeJuizes' => $this->getQuantidadeJuizes(),
            'pontuacaoMaxima' => $this->pontuacaoMaxima,
            'criteriosAvaliacao' => $this->criteriosAvaliacao,
            'vencedor' => $this->getVencedor(),
            'criadoEm' => $this->getCriadoEm()->format('Y-m-d H:i:s'),
            'atualizadoEm' => $this->getAtualizadoEm()->format('Y-m-d H:i:s')
        ];
    }
}
