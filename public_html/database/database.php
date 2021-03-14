<?php
    require_once __DIR__ . '/crud.php';
    include_once __DIR__ . '/../mapping.php';
    
    class Database
    {
        private $crud;


        public function __construct(PDO $databaseConnection) 
        { 
            $this->crud = new Crud($databaseConnection);
        }


        /** 
         * Gets all users 
         * 
         * @param bool $notAnswers 
         * 
         * @return array|null Array contain User objects
         */
        public function getAllUsers(bool $notAnswers = true): ?array
        {
            $where = $notAnswers ? "" : "WHERE Answers = 0";

            $sql = "SELECT Name, Answers FROM User $where ORDER BY Answers ASC, Name ASC";

            $users = [];
            foreach ($this->crud->select($sql) as $user)
            {
                $users[] = new User($user->Name, $user->Answers);
            } 

            return $users;
        }


        /** 
         * Get a user
         * 
         * @param string $user Name of the user
         * 
         * @return User|null Object of User
         */
        public function getUser(string $user): ?User
        {
            $sql = "SELECT Name, Answers FROM User WHERE Name = ?";
            $user = $this->crud->select($sql, [$user]);

            return !is_null($user) ? new User($user->Name, $user->Answers) : null;
        }


        /** 
         * Gets a list of all matches 
         * 
         * @param int $season Year the season started
         * @param int $weekNum Week number to display
         * @param int $user Name of the user
         * 
         * @return array|null Array contains Match objects
         */
        public function weekGames(int $seasonStart, int $weekNum, string $user): ?array 
        {
            $sql = "SELECT ch.FullName AS HomeTeam, ca.FullName AS AwayTeam, sc.HomeScore, sc.AwayScore, g.KickOff, 
                        sc.Win, sc.Draw, sc.Lose, sc.Joker FROM Season s 
                    INNER JOIN Week w ON s.SeasonID = w.SeasonID 
                    INNER JOIN Game g ON w.WeekID = g.WeekID 
                    INNER JOIN Score sc ON g.GameID = sc.GameID 
                    INNER JOIN Club ch ON g.HomeTeam = ch.ClubID 
                    INNER JOIN Club ca ON g.AwayTeam = ca.ClubID 
                    INNER JOIN User u ON sc.UserID = u.UserID 
                    WHERE YEAR(s.StartDate) = ? AND w.WeekNum = ? AND u.Name = ? AND g.Postponed = 0
                    ORDER BY KickOff ASC, ch.FullName ASC";

            $rows = $this->crud->select($sql, [$seasonStart, $weekNum, $user]);

            $matchNum = 0;
            $matches = [];
            foreach ($rows as $row)
            {
                $homeTeam = $this->getClub($row->HomeTeam);
                $awayTeam = $this->getClub($row->AwayTeam);
                $homeScore = $row->HomeScore;
                $awayScore = $row->AwayScore;
                $kickOff = new DateTime($row->KickOff);
                $win = $row->Win;
                $draw = $row->Draw;
                $lose = $row->Lose;
                $joker = $row->Joker;

                $matches[] = new Game(++$matchNum, $homeTeam, $awayTeam, $homeScore, $awayScore, $kickOff, $win, $draw, $lose, $joker);
            }

            return $matches;
        }


        /**  */
        public function getCurrentWeek(int $seasonStart, int $weekNum = 0): Week
        {
            
            // Select current week 
            if ($weekNum == 0) 
            {
                // Current Date
                $today = new DateTime("now", new DateTimeZone('Europe/London')); 
                $today = $today->format('Y-m-d');


                // Get current or next week
                $sql = "SELECT w.WeekNum, w.StartTime, w.EndDate FROM Week w 
                        INNER JOIN Season s ON w.SeasonID = s.SeasonID 
                        WHERE YEAR(s.StartDate) = ? AND w.EndDate >= ? 
                        ORDER BY w.EndDate ASC
                        LIMIT 1";
                
                $row = $this->crud->select($sql, [$seasonStart, $today]);


                // If no more weeks
                if (is_null($row)) 
                {
                    // Get final week
                    $sql = "SELECT w.WeekNum, w.StartTime, w.EndDate FROM Week w 
                            INNER JOIN Season s ON w.SeasonID = s.SeasonID 
                            WHERE YEAR(s.StartDate) = ? AND w.EndDate < ? 
                            ORDER BY w.EndDate DESC
                            LIMIT 1";

                    $row = $this->crud->select($sql, [$seasonStart, $today]);
                }
            }
            else {
                $sql = "SELECT w.WeekNum, w.StartTime, w.EndDate FROM Week w 
                        INNER JOIN Season s ON w.SeasonID = s.SeasonID 
                        WHERE YEAR(s.StartDate) = ? AND w.WeekNum = ?
                        LIMIT 1";

                $row = $this->crud->select($sql, [$seasonStart, $weekNum]);
            }
            
            
            return new Week(intval($row->WeekNum), new DateTime($row->StartTime), new DateTime($row->EndDate));
        }


        /**  */
        public function getWeekScore(int $seasonStart, int $weekNum, string $user) 
        {
            $sql = "SELECT ws.Score FROM Season s  
                    INNER JOIN Week w ON s.SeasonID = w.SeasonID 
                    INNER JOIN WeekScore ws ON w.WeekID = ws.WeekID 
                    INNER JOIN User u ON ws.UserID = u.UserID 
                    WHERE YEAR(s.StartDate) = ? AND w.WeekNum = ? AND u.Name = ?";

            return $this->crud->select($sql, [$seasonStart, $weekNum, $user])->{"Score"};
        }


        public function getCurrentWeekScores(int $seasonStart, string $user) 
        {
            $today = new DateTime("now", new DateTimeZone('Europe/London')); 

            $sql = "SELECT ws.`Score` FROM `Season` s  
                    INNER JOIN `Week` w ON s.`SeasonID` = w.`SeasonID` 
                    INNER JOIN `WeekScore` ws ON w.`WeekID` = ws.`WeekID` 
                    INNER JOIN `User` u ON ws.`UserID` = u.`UserID`
                    WHERE YEAR(s.`StartDate`) = ? AND w.`StartTime` <= ? AND u.`Name` = ? 
                    ORDER BY w.`StartTime` DESC
                    LIMIT 1";
            
            return $this->crud->select($sql, [$seasonStart, $today->format('Y-m-d H:i'), $user])->{"Score"};
        }


        /**  */
        public function setScores(string $user, int $seasonStart, int $weekNum, Game $match, int $homeScore, int $awayScore, $joker = null)
        {   
            $week_Sub = "SELECT w.WeekID FROM Week w
                         INNER JOIN Season s ON w.SeasonID = s.SeasonID 
                         WHERE WeekNum = ? AND YEAR(s.StartDate) = ?";
            
            $club_Sub = "SELECT c.ClubID FROM Club c WHERE c.Name = ?";

            $game_Sub = "SELECT g.GameID FROM Game g 
                         WHERE WeekID = ($week_Sub) 
                         AND HomeTeam = ($club_Sub) 
                         AND AwayTeam = ($club_Sub)";

            $user_Sub = "SELECT u.UserID FROM User u WHERE u.Name = ?";

            
            $homeTeam = $match->getHomeTeam()->getName();
            $awayTeam = $match->getAwayTeam()->getName();

            if (is_null($joker)) 
            {
                $sql = "UPDATE Score 
                        SET HomeScore = ?, AwayScore = ? 
                        WHERE GameID = ($game_Sub) 
                        AND UserID = ($user_Sub)";
                
                $this->crud->runSQL($sql, [$homeScore, $awayScore, $weekNum, $seasonStart, $homeTeam, $awayTeam, $user]);
            }
            else
            {
                $joker = $joker ? 1 : 0;

                $sql = "UPDATE Score 
                        SET HomeScore = ?, AwayScore = ?, Joker = ? 
                        WHERE GameID = ($game_Sub) 
                        AND UserID = ($user_Sub)";

                $this->crud->runSQL($sql, [$homeScore, $awayScore, $joker, $weekNum, $seasonStart, $homeTeam, $awayTeam, $user]);
            }					
        }


        /**  */
        public function getWeekScores(int $season): ?array
        {
            $today = new DateTime("now", new DateTimeZone('Europe/London')); // Current Date

            $sql = "SELECT w.WeekNum, ws.Score FROM Season s  
                    INNER JOIN Week w ON s.SeasonID = w.SeasonID 
                    INNER JOIN WeekScore ws ON w.WeekID = ws.WeekID 
                    INNER JOIN User u ON ws.UserID = u.UserID 
                    WHERE YEAR(s.StartDate) = ? AND w.StartTime <= ?
                    ORDER BY w.WeekNum ASC, u.UserID ASC";
            
            $rows = $this->crud->select($sql, [$season, $today->format('Y-m-d H:i')]);


            $scores = [];
            $week = [];
            $weekNum = 0;
            for ($i = 0; $i <= count($rows); $i++)
            {
                // End of rows count
                if ($i == count($rows))
                {
                    $scores[] = $week;
                    break;
                }

                // If new week
                if ($rows[$i]->WeekNum != $weekNum)
                {
                    // If first iteration
                    if ($i == 0)
                        $scores = [];
                    else 
                        $scores[] = $week;

                    // Create new week
                    $weekNum = $rows[$i]->WeekNum;
                    $week = [];
                    $week[] = $weekNum;
                }

                // Add scores
                $week[] = $rows[$i]->Score;
            }

            return $scores;
        }


        /**  */
        public function totalWeeks(int $season): int
        {
            $sql = "SELECT COUNT(w.WeekNum) AS WeekCount FROM Week w
                    INNER JOIN Season s ON w.SeasonID = s.SeasonID
                    WHERE YEAR(s.StartDate) = ?";

            return $this->crud->select($sql, [$season])->{"WeekCount"};
        }
        

        /**  */
        public function getSeasonScore(int $season): ?array
        {
            $sql = "SELECT u.Name, ss.Score FROM Season s 
                    INNER JOIN SeasonScore ss ON s.SeasonID = ss.SeasonID 
                    INNER JOIN User u ON ss.UserID = u.UserID
                    WHERE YEAR(s.StartDate) = ? 
                    ORDER BY u.UserID ASC";
					
            return $this->crud->select($sql, [$season]);
        }


        public function leaderboard(int $season) 
        {
            $today = new DateTime("now", new DateTimeZone('Europe/London')); 

            $sql = "SELECT u.`Name`, ss.`Score` AS SeasonScore, ws.`Score` AS WeekScore FROM `User` u
                    INNER JOIN `SeasonScore` ss ON u.`UserID` = ss.`UserID`
                    INNER JOIN `Season` s ON ss.`SeasonID` = s.`SeasonID`
                    INNER JOIN `WeekScore` ws ON u.`UserID` = ws.`UserID`
                    INNER JOIN `Week` w ON ws.`WeekID` = (
                        SELECT `WeekID` FROM `Week`
                        WHERE `StartTime` <= ?
                        ORDER BY `StartTime` DESC
                        LIMIT 1
                    )
                    WHERE YEAR(s.`StartDate`) = ?
                    GROUP BY u.`Name`
                    ORDER BY ss.`Score` DESC, ws.`Score` DESC, u.`Name` ASC";
            
            return $this->crud->select($sql, [$today->format('Y-m-d H:i'), $season]);
        }

        /**  */
        public function getLeagueTable()
        {
            $positions = [];

            $won_Case = "CASE WHEN (s.HomeScore > s.AwayScore AND c.ClubID = g.HomeTeam) OR (s.AwayScore > s.HomeScore AND c.ClubID = g.AwayTeam) THEN 1 ELSE 0 END";
            $drawn_Case = "CASE WHEN s.HomeScore = s.AwayScore AND (c.ClubID = g.HomeTeam OR c.ClubID = g.AwayTeam) THEN 1 ELSE 0 END";
            $lost_Case = "CASE WHEN (s.HomeScore < s.AwayScore AND c.ClubID = g.HomeTeam) OR (s.AwayScore < s.HomeScore AND c.ClubID = g.AwayTeam) THEN 1 ELSE 0 END";
            $for_Case = "CASE WHEN g.HomeTeam = c.ClubID THEN s.HomeScore ELSE s.AwayScore END";
            $against_Case = "CASE WHEN g.AwayTeam = c.ClubID THEN s.HomeScore ELSE s.AwayScore END";

            $user_Sub = "SELECT u.UserID FROM User u WHERE u.Answers = true";

            foreach ($this->getAllClubs() as $club)
            {
                $sql = "SELECT SUM($won_Case) AS Won, SUM($drawn_Case) AS Drawn, SUM($lost_Case) AS Lost, 
                               SUM($for_Case) AS GoalsFor, SUM($against_Case) AS GoalsAgainst FROM Club c 
                        INNER JOIN Game g ON c.ClubID = g.HomeTeam OR c.ClubID = g.AwayTeam
                        INNER JOIN Score s ON g.GameID = s.GameID
                        INNER JOIN Week w ON w.WeekID = g.WeekID
                        WHERE c.FullName = ? AND UserID = ($user_Sub) AND s.HomeScore >= 0 AND s.AwayScore >= 0 AND w.SeasonID = 3
                        GROUP BY c.FullName
                        ORDER BY c.FullName ASC";
                
                $pos = $this->crud->select($sql, [$club->getFullName()]);

                $positions[] = new Position($club, intval($pos->Won), intval($pos->Drawn), intval($pos->Lost), intval($pos->GoalsFor), intval($pos->GoalsAgainst));
            }
            
            usort($positions, function(Position $a, Position $b) { 
                $a_Pts = $a->getPoints();
                $b_Pts = $b->getPoints();

                if (($a_Pts - $b_Pts) < 0)
                    return $b_Pts - $a_Pts; 
                else if ($a_Pts == $b_Pts)
                {
                    $a_GD = $a->getGoalDifference();
                    $b_GD = $b->getGoalDifference();

                    if (($a_GD - $b_GD) < 0)
                        return $b_GD - $a_GD;
                    else if ($a_GD == $b_GD)
                    {
                        $a_GF = $a->getGoalsFor();
                        $b_GF = $b->getGoalsFor();

                        if (($a_GF - $b_GF) < 0)
                            return $b_GF - $a_GF;
                        else if ($a_GF == $b_GF) 
                        {
                            $a_Name = $a->getClub()->getFullName();
                            $b_Name = $b->getClub()->getFullName();

                            if ($a_Name < $b_Name)
                                return $b_Name - $a_Name;
                        }
                    }
                }
            });

            $league = new League();
            $league->setPositions($positions);
            return $league;
        }


        /**  */
        private function getAllClubs(): array
        {
            $sql = "SELECT Name, FullName, Abbreviation FROM Club WHERE Relegated = 0 ORDER BY FullName ASC";

            $allClubs = [];
            foreach ($this->crud->select($sql) as $club)
            {
                $allClubs[] = new Club($club->Name, $club->FullName, $club->Abbreviation);
            }

            return $allClubs;
        }


        /**  */
        private function getClub(string $fullName): Club
        {
            $sql = "SELECT Name, FullName, Abbreviation FROM Club WHERE Relegated = 0 AND FullName = ?";
            $club = $this->crud->select($sql, [$fullName]);

            return new Club($club->Name, $club->FullName, $club->Abbreviation);
        }


        /**  */
        public function getSeason(int $season) 
        {
            $sql = "SELECT StartDate, EndDate FROM Season WHERE Year(StartDate) = ?";
            $season = $this->crud->select($sql, [$season]);

            return new Season(new DateTime($season->StartDate), new DateTime($season->EndDate));
        }



        public function getClubsForm(string $fullName, DateTime $from, int $numOfGames = 5) 
        {
            $from = $from->format("Y-m-d H:i");
            $today = new DateTime("now", new DateTimeZone('Europe/London')); 
            $today = $today->format("Y-m-d H:i");

            $sql = "SELECT ch.Abbreviation AS HomeAbbr, ch.Name AS HomeName, ch.FullName AS HomeFull, s.HomeScore, 
                    ca.Abbreviation AS AwayAbbr, ca.Name AS AwayName, ca.FullName AS AwayFull, s.AwayScore FROM Score s 
                    INNER JOIN Game g ON s.GameID = g.GameID
                    INNER JOIN Club ch ON g.HomeTeam = ch.ClubID
                    INNER JOIN Club ca ON g.AwayTeam = ca.ClubID
                    WHERE (ch.FullName = ? OR ca.FullName = ?) AND g.KickOff < IF(? > ?, ?, ?) 
                        AND g.Postponed = 0 AND s.UserID = 1
                    ORDER BY g.KickOff DESC
                    LIMIT ?";
            $form = $this->crud->select($sql, [$fullName, $fullName, $from, $today, $today, $from, $numOfGames]);

            return is_object($form) ? [$form] : $form;
        }



        function view($var)
        {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
    }
?>