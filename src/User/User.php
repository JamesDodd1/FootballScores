<?php
namespace Source\User;

class User
{
    private $name, $isAnswers;

    public function __construct(string $name, bool $isAnswers = false)
    {
        $this->name = $name;
        $this->isAnswers = $isAnswers; 
    }

    public function getName(): string    { return $this->name; }
    public function getIsAnswers(): bool { return $this->isAnswers; }
}
?>
