<?php

namespace Hogwarts\Models\Module1;

use Hogwarts\Models\Base\Person;
use Hogwarts\Models\Enums\StudentStatus;
use Hogwarts\Models\Enums\HouseType;

class Student extends Person
{
    private StudentStatus $status;
    private ?HouseType $casa = null;
    private int $ano;
    private array $materias = [];
    private int $pontos = 0;
    private array $registrosDisciplinares = [];
    private ?string $tipoVarinha = null;
    private ?string $patrono = null;
    private array $habilidades = [];
    private array $conquistas = [];
    private ?string $statusSangue = null;
    private array $membrosFamilia = [];
    private array $registrosMedicos = [];

    public function __construct(string $nome, string $email, \DateTime $dataNascimento, string $endereco = '')
    {
        parent::__construct($nome, $email, $dataNascimento, $endereco);
        $this->status = StudentStatus::CONVIDADO;
        $this->ano = 1;
    }

    public function getStatus(): StudentStatus
    {
        return $this->status;
    }

    public function setStatus(StudentStatus $status): void
    {
        $this->status = $status;
        $this->atualizarTimestamp();
    }

    public function getCasa(): ?HouseType
    {
        return $this->casa;
    }

    public function setCasa(HouseType $casa): void
    {
        $this->casa = $casa;
        $this->atualizarTimestamp();
    }

    public function getAno(): int
    {
        return $this->ano;
    }

    public function setAno(int $ano): void
    {
        if ($ano < 1 || $ano > 7) {
            throw new \InvalidArgumentException("Ano escolar deve estar entre 1 e 7");
        }
        $this->ano = $ano;
        $this->atualizarTimestamp();
    }

    public function avancarAno(): bool
    {
        if ($this->ano < 7) {
            $this->ano++;
            $this->atualizarTimestamp();
            return true;
        }
        return false;
    }

    public function getPontos(): int
    {
        return $this->pontos;
    }

    public function adicionarPontos(int $pontos): void
    {
        if ($pontos <= 0) {
            throw new \InvalidArgumentException("Pontos adicionados devem ser positivos");
        }
        $this->pontos += $pontos;
        $this->atualizarTimestamp();
    }

    public function removerPontos(int $pontos): void
    {
        if ($pontos <= 0) {
            throw new \InvalidArgumentException("Pontos removidos devem ser positivos");
        }
        $this->pontos = max(0, $this->pontos - $pontos);
        $this->atualizarTimestamp();
    }

    public function getMaterias(): array
    {
        return $this->materias;
    }

    public function adicionarMateria(string $materia): void
    {
        if (!in_array($materia, $this->materias)) {
            $this->materias[] = $materia;
            $this->atualizarTimestamp();
        }
    }

    public function removerMateria(string $materia): void
    {
        $chave = array_search($materia, $this->materias);
        if ($chave !== false) {
            unset($this->materias[$chave]);
            $this->materias = array_values($this->materias);
            $this->atualizarTimestamp();
        }
    }

    public function getRegistrosDisciplinares(): array
    {
        return $this->registrosDisciplinares;
    }

    public function adicionarRegistroDisciplinar(string $registro, string $emitidoPor, string $gravidade = 'normal'): void
    {
        $this->registrosDisciplinares[] = [
            'registro' => $registro,
            'data' => new \DateTime(),
            'emitidoPor' => $emitidoPor,
            'gravidade' => $gravidade
        ];
        $this->atualizarTimestamp();
    }

    public function getTipoVarinha(): ?string
    {
        return $this->tipoVarinha;
    }

    public function setTipoVarinha(string $tipoVarinha): void
    {
        $this->tipoVarinha = $tipoVarinha;
        $this->atualizarTimestamp();
    }

    public function getPatrono(): ?string
    {
        return $this->patrono;
    }

    public function setPatrono(string $patrono): void
    {
        $this->patrono = $patrono;
        $this->atualizarTimestamp();
    }

    public function getHabilidades(): array
    {
        return $this->habilidades;
    }

    public function adicionarHabilidade(string $habilidade, int $nivel = 1): void
    {
        $this->habilidades[$habilidade] = $nivel;
        $this->atualizarTimestamp();
    }

    public function melhorarHabilidade(string $habilidade, int $incremento = 1): void
    {
        if (isset($this->habilidades[$habilidade])) {
            $this->habilidades[$habilidade] += $incremento;
            $this->atualizarTimestamp();
        }
    }

    public function getConquistas(): array
    {
        return $this->conquistas;
    }

    public function adicionarConquista(string $conquista, \DateTime $data = null): void
    {
        $this->conquistas[] = [
            'titulo' => $conquista,
            'data' => $data ?? new \DateTime()
        ];
        $this->atualizarTimestamp();
    }

    public function getStatusSangue(): ?string
    {
        return $this->statusSangue;
    }

    public function setStatusSangue(string $statusSangue): void
    {
        $statusValidos = ['puro-sangue', 'mestiço', 'nascido-trouxa', 'desconhecido'];
        if (!in_array(strtolower($statusSangue), $statusValidos)) {
            throw new \InvalidArgumentException("Status de sangue inválido");
        }
        $this->statusSangue = strtolower($statusSangue);
        $this->atualizarTimestamp();
    }

    public function getMembrosFamilia(): array
    {
        return $this->membrosFamilia;
    }

    public function adicionarMembroFamilia(string $nome, string $parentesco): void
    {
        $this->membrosFamilia[] = [
            'nome' => $nome,
            'parentesco' => $parentesco
        ];
        $this->atualizarTimestamp();
    }

    public function getRegistrosMedicos(): array
    {
        return $this->registrosMedicos;
    }

    public function adicionarRegistroMedico(string $condicao, string $tratamento = '', bool $resolvido = false): void
    {
        $this->registrosMedicos[] = [
            'condicao' => $condicao,
            'tratamento' => $tratamento,
            'data' => new \DateTime(),
            'resolvido' => $resolvido
        ];
        $this->atualizarTimestamp();
    }

    public function atualizarRegistroMedico(int $indice, bool $resolvido): void
    {
        if (isset($this->registrosMedicos[$indice])) {
            $this->registrosMedicos[$indice]['resolvido'] = $resolvido;
            $this->atualizarTimestamp();
        }
    }

    public function podeFrequentarAulas(): bool
    {
        return $this->status->podeFrequentarAulas();
    }

    public function temProblemasmedicos(): bool
    {
        foreach ($this->registrosMedicos as $registro) {
            if (!$registro['resolvido']) {
                return true;
            }
        }
        return false;
    }

    public function getMediaPontos(): float
    {
        if ($this->ano === 1) {
            return $this->pontos;
        }
        return $this->pontos / $this->ano;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'email' => $this->getEmail(),
            'dataNascimento' => $this->getDataNascimento()->format('Y-m-d'),
            'idade' => $this->getIdade(),
            'endereco' => $this->getEndereco(),
            'status' => $this->status->value,
            'casa' => $this->casa?->value,
            'ano' => $this->ano,
            'pontos' => $this->pontos,
            'materias' => $this->materias,
            'tipoVarinha' => $this->tipoVarinha,
            'patrono' => $this->patrono,
            'statusSangue' => $this->statusSangue,
            'podeFrequentarAulas' => $this->podeFrequentarAulas(),
            'temProblemasmedicos' => $this->temProblemasmedicos(),
            'criadoEm' => $this->getCriadoEm()->format('Y-m-d H:i:s'),
            'atualizadoEm' => $this->getAtualizadoEm()->format('Y-m-d H:i:s')
        ];
    }
}
