<?php
namespace Source\Club;

class Club
{
    private $name, $fullName, $abbreviation;

    /**
     * @param string $name         Shortened name
     * @param string $fullName     Full name
     * @param string $abbreviation Abbreviated name
     */
    public function __construct(string $name, string $fullName, string $abbreviation)
    {
        $this->name = $name;
        $this->fullName = $fullName;
        $this->abbreviation = $abbreviation;
    }

    public function getName(): string     { return $this->name; }
    public function getFullName(): string { return $this->fullName; }
    public function getAbbreviate(): string { return $this->abbreviation; }
}
?>
