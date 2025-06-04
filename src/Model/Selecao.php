<?php

namespace Hogwarts\Models\Module2;

use Hogwarts\Models\Module1\Student;
use Hogwarts\Models\Enums\HouseType;

class SortingHat
{
    private array $questions = [
        'Qual qualidade você mais valoriza?' => [
            'Coragem' => HouseType::GRYFFINDOR,
            'Inteligência' => HouseType::RAVENCLAW,
            'Lealdade' => HouseType::HUFFLEPUFF,
            'Ambição' => HouseType::SLYTHERIN
        ],
        'Em uma situação de perigo, você:' => [
            'Enfrenta de frente' => HouseType::GRYFFINDOR,
            'Analisa antes de agir' => HouseType::RAVENCLAW,
            'Protege os outros primeiro' => HouseType::HUFFLEPUFF,
            'Busca a melhor estratégia' => HouseType::SLYTHERIN
        ],
        'Seu maior medo é:' => [
            'Ser covarde' => HouseType::GRYFFINDOR,
            'Ser ignorante' => HouseType::RAVENCLAW,
            'Ser rejeitado' => HouseType::HUFFLEPUFF,
            'Ser fraco' => HouseType::SLYTHERIN
        ]
    ];

    public function getQuestions(): array
    {
        return array_keys($this->questions);
    }

    public function getAnswersForQuestion(string $question): array
    {
        return array_keys($this->questions[$question] ?? []);
    }

    public function sortStudent(array $answers): HouseType
    {
        $houseScores = [
            HouseType::GRYFFINDOR->value => 0,
            HouseType::HUFFLEPUFF->value => 0,
            HouseType::RAVENCLAW->value => 0,
            HouseType::SLYTHERIN->value => 0
        ];

        foreach ($answers as $questionIndex => $answer) {
            $question = array_keys($this->questions)[$questionIndex] ?? null;
            if ($question && isset($this->questions[$question][$answer])) {
                $house = $this->questions[$question][$answer];
                $houseScores[$house->value]++;
            }
        }

        // Se houver empate, escolhe aleatoriamente entre as casas empatadas
        $maxScore = max($houseScores);
        $topHouses = array_keys(array_filter($houseScores, fn($score) => $score === $maxScore));
        
        $selectedHouse = $topHouses[array_rand($topHouses)];
        
        return HouseType::from($selectedHouse);
    }

    public function manualSort(Student $student, HouseType $house): void
    {
        $student->setHouse($house);
    }
}
