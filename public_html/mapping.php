
<?php
    /**  */
    class User
    {
        private $name, $isAnswers;

        public function __construct(string $name, bool $isAnswers)
        {
            $this->name = $name;
            $this->isAnswers = $isAnswers; 
        }

        public function getName(): string    { return $this->name; }
        public function getIsAnswers(): bool { return $this->isAnswers; }
    }


    /**  */
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


    /**  */
    class Season 
    {
        private $name, $start, $end, $week;

        /**
         * @param DateTime $start Beginning date of the season
         * @param DateTime $end   End date of the season
         */
        public function __construct(DateTime $start, DateTime $end) 
        { 
            $this->start = $start;
            $this->end = $end;

            $this->name = "";
            $this->week = [];
        }

        public function getName(): string    { return $this->name; }
        public function getStart(): DateTime { return $this->start; }
        public function getEnd(): DateTime   { return $this->end; }
        /** 
         * @return array[Week] 
         * */
        public function getWeek(): array     { return $this->week; }

        /** 
         * @param array[Week] $week
         */
        public function setWeek(array $week) { $this->week = $week; }

        //protected function getSeason() { return new self($this->start, $this->end); }
    }


    /**  */
    class Week //extends Season
    {
        private $weekNum, $startTime, $endDate, $matches;

        public function __construct(int $weekNum, DateTime $start, DateTime $end) 
        { 
            $this->weekNum = $weekNum; 
            $this->startTime = $start;
            $this->endDate = $end;

            $this->matches = [];
        }

        public function getWeekNum(): int        { return $this->weekNum; }
        public function getStart(): DateTime     { return $this->startTime; }
        public function getEnd(): DateTime       { return $this->endDate; }
        public function getMatches(): array      { return $this->matches; }

        public function setMatches(array $matches) { $this->matches = $matches; }

        //public function getSeasonStart(): DateTime { return parent::getStart(); }
        //public function getSeasonEnd(): DateTime   { return parent::getEnd(); }

        //protected function getSeason() { return parent::getSeason(); }
    }


    /**  */
    class Game //extends Week
    {
        private $matchNum, $homeTeam, $awayTeam, $homeScore, $awayScore, $kickOff, $win, $draw, $lose, $joker;

        /**  */
        public function __construct(int $matchNum, Club $homeTeam, Club $awayTeam, int $homeScore, int $awayScore, DateTime $kickOff, bool $win, bool $draw, bool $lose, bool $joker)
        {
            $this->matchNum = $matchNum > 0 ? $matchNum : 1;

            $this->homeTeam = $homeTeam;
            $this->awayTeam = $awayTeam;

            $this->homeScore = $homeScore >= -1 ? $homeScore : -1;
            $this->awayScore = $awayScore >= -1 ? $awayScore : -1;

            $this->kickOff = $kickOff;

            $this->win = $win;
            $this->draw = $draw;
            $this->lose = $lose;

            $this->joker = $joker;
        }

        public function getMatchNum(): int     { return $this->matchNum; }
        public function getHomeTeam(): Club    { return $this->homeTeam; }
        public function getAwayTeam(): Club    { return $this->awayTeam; }
        public function getHomeScore(): int    { return $this->homeScore; }
        public function getAwayScore(): int    { return $this->awayScore; }
        public function getKickOff(): DateTime { return $this->kickOff; }
        public function getWin(): bool         { return $this->win; }
        public function getDraw(): bool        { return $this->draw; }
        public function getLose(): bool        { return $this->lose; }
        public function getJoker(): bool       { return $this->joker; }

        //public function WeekStart() { return parent::getStart(); }
        //public function WeekEnd()   { return parent::getEnd(); }
    }


    /**  */
    class League
    {
        private $positions, $numOfChampionsLeague, $numOfRelegated;

        public function __construct()
        {
            $this->positions = [];
            $this->numOfChampionsLeague = 4;
            $this->numOfRelegated = 3; 
        }

        public function getPositions(): array        { return $this->positions; }
        public function getChampionsLeaguePos(): int { return $this->numOfChampionsLeague; }
        public function getRelegationPos(): int       { return count($this->positions) - $this->numOfRelegated; }

        public function setPositions(array $positions) { $this->positions = $positions; }
    }


    /**  */
    class Position 
    {
        private $club, $won, $drawn, $lost, $goalsFor, $goalsAgainst;

        /**
         * @param Club $club
         * @param int $won
         * @param int $drawn
         * @param int $lost
         * @param int $goalsFor
         * @param int $goalsAgainst
         */
        public function __construct(Club $club, int $won, int $drawn, int $lost, int $goalsFor, int $goalsAgainst)
        {
            $this->club = $club;
            $this->won = $won;
            $this->drawn = $drawn;
            $this->lost = $lost;
            $this->goalsFor = $goalsFor;
            $this->goalsAgainst = $goalsAgainst;
        }

        public function getPlayed(): int         { return $this->won + $this->drawn + $this->lost; }
        public function getClub(): Club          { return $this->club; }
        public function getWon(): int            { return $this->won; }
        public function getDrawn(): int          { return $this->drawn; }
        public function getLost(): int           { return $this->lost; }
        public function getGoalsFor(): int       { return $this->goalsFor; }
        public function getGoalsAgainst(): int   { return $this->goalsAgainst; }
        public function getGoalDifference(): int { return $this->goalsFor - $this->goalsAgainst; }
        public function getPoints(): int         { return $this->won * 3 + $this->drawn; } 
    }
?>