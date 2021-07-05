<?php
namespace Source\Table;

class EurosGroup implements ILeagueTable
{
    protected $name, $positions;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string     { return $this->name; }
    public function getPositions(): array { return $this->positions; }

    public function setPositions(array $positions) { $this->positions = $positions; }
}
?>