
<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
	include_once "$root/database/database.php";
    $db = new Database();

    
    $isWeekNumSet = isset($_REQUEST['weekNum']);
    $weekNum = $isWeekNumSet ? intval($_REQUEST['weekNum']) : 0;
    

    $weekScoreText = "&nbsp";
    if (!$user->getIsAnswers()) {
        $weekScore = $db->getWeekScore($season, $weekNum, $user->getName());

        $class = "class='weekScore'";
        $weekScoreText = "<b> Score: $weekScore </b>";

        $col = "secondCol";
    }
?>

<ul id="weekNum">
    <li <?php echo $class; ?> style="width: 10%;">
        <?php echo $weekScoreText; // Temp ?>
    </li>

    <li class="weekSelect <?php echo $col; ?>" style="width: 80%;">
        <?php echo (new WeekNumSelector())->weekSelectorHTML(2020, $weekNum); ?>
    </li>

    <li style="width: 10%;"> &nbsp; </li>
</ul>


<?php
    class WeekNumSelector {
        public function __construct() { }


        public function weekSelectorHTML(int $season, int $selectedWeekNum = 0)
        {
            $db = new Database();

            if ($selectedWeekNum == 0)
                $selectedWeekNum = ($db->getCurrentWeek($season, $selectedWeekNum))->getWeekNum();


            $weekNumCount = $db->totalWeeks($season);
            
            $leftArrowHTML = $this->leftArrowHTML($selectedWeekNum);
            $weekNumDropdownHTML = $this->weekNumDropdownHTML($season, $selectedWeekNum);
            $rightArrowHTML = $this->rightArrowHTML($selectedWeekNum, $weekNumCount);


            return 
                "$leftArrowHTML
                $weekNumDropdownHTML
                $rightArrowHTML";
        }


        private function weekNumDropdownHTML(int $season, int $selectedWeekNum)
        {
            $weekNumCount = (new Database())->totalWeeks($season);


            $weekNumOptions = "";
            for ($i = 1; $i <= $weekNumCount; $i++) {

                $weekIsSelected = $selectedWeekNum == $i;
                
                if ($weekIsSelected)
                    $weekNumOptions.= "<option value='$i' selected> Week $i </option>";
                else
                    $weekNumOptions.= "<option value='$i'> Week $i </option>";
            }
            
            return 
                "<select class='weekCombo' name='weekNum' onchange='loadNewWeek(this.value)'>
                    $weekNumOptions
                </select>";
        }


        private function leftArrowHTML(int $selectedWeekNum)
        {
            $arrow = $this->arrow($selectedWeekNum - 1, 0);
            
            return
                "<button onclick='loadNewWeek(" . ($selectedWeekNum - 1) . ")' class='arrowButton' name='weekNum' value='$arrow->newWeekNum' $arrow->isEnabled>
                    <img class='arrow' src='$arrow->image' alt='Left Arrow' style='transform: rotate(180deg);' />
                </button>";
        }


        private function rightArrowHTML(int $selectedWeekNum, int $finalWeekNum)
        {
            $arrow = $this->arrow($selectedWeekNum + 1, $finalWeekNum + 1);
        
            return 
                "<button onclick='loadNewWeek(" . ($selectedWeekNum + 1) . ")' class='arrowButton' name='weekNum' value='$arrow->newWeekNum' $arrow->isEnabled>
                    <img class='arrow' src='$arrow->image' alt='Right Arrow' />
                </button>";
        }


        private function arrow(int $newWeekNum, int $finalWeek)
        {
            if ($newWeekNum == $finalWeek) 
            {
                return (object) [
                    "newWeekNum" => $newWeekNum,
                    "isEnabled" => "disabled",
                    "image" => "/images/Arrow_Disabled.png"
                ];
            }
            
                
            return (object) [
                "newWeekNum" => $newWeekNum,
                "isEnabled" => "",
                "image" => "/images/Arrow_Enabled.png"
            ];
        }
    }
    $abc = 1;
?>


<script type="text/javascript">
    //console.log(location.protocol);
    //console.log(location.host);
    //console.log(location.pathname);
    
    function loadNewWeek(newWeekNum) {
        let isResults = <?php echo $user->getIsAnswers() ? 1 : 0 ?>;

        //let url = location.protocol + '//' + location.host + location.pathname;
        let parameters = "";

        if (isResults) 
            parameters = "/" + newWeekNum;
        else
            parameters = "/" + "<?php echo $user->getName(); ?>" + "/" + newWeekNum;

        //console.log("New URL: " + "/games" + parameters);
        window.location.replace("/games" + parameters);
    }
</script>
