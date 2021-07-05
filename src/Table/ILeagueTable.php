<?php
namespace Source\Table;

interface ILeagueTable
{
    public function getName(): string;
    public function getPositions(): array;

    public function setPositions(array $positions);
}
?>
