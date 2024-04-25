<?php

  define('__CORE_TYPE__','view');
  include $_SERVER['DOCUMENT_ROOT'].'/function/core.php';


	if(isset($_POST['year']) === true){
		$year = $_POST['year'];
	}else{
		//전달받은 Year값
		$year = date('Y');
	}
	if(isset($_POST['month']) === true){
		$month = $_POST['month'];
	}else{
		//전달받은 Month값
		$month = date('m');
	}

	//현재 달의 총 날짜
	$total_day = date('t', strtotime("$year-$month-01"));


	//요일 배열
	$weekString = array("일", "월", "화", "수", "목", "금", "토");



	//그달의 첫 째 요일(시작요일)
	$firstDayofMonth = date('Y-m-d', strtotime("$year-$month-01"));
	$firstDayWeekdayIndex = date('w', strtotime($firstDayofMonth));
	$firstDayWeekday = $weekString[$firstDayWeekdayIndex];

	//그달의 마지막 날짜
	$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$lastDayOfMonth = date('Y-m-d', strtotime("$year-$month-$daysInMonth"));

	//그달의 마지막 요일
	$lastDayWeekdayIndex = date('w', strtotime($lastDayOfMonth));
	$lastDayWeekday = $weekString[$lastDayWeekdayIndex];

	
?>

<div id="calendar">
    <div><?php echo $year ?>년<?php echo $month ?>월</div>
    <button onclick="prev();">이전달</button>
    <button onclick="next();">다음달</button>
    <script>
    	// 지난 달 버튼 클릭시 호출되는 함수
					function prev() {
					    var prevMonth;
					    var year = <?php echo $year; ?>;
					    if(<?php echo $month; ?> === 1){
					        prevMonth = 12;
					        year = <?php echo $year; ?> - 1;
					    } else {
					        prevMonth = <?php echo $month; ?> - 1;  
					    }
					    updateCalendar(year, prevMonth);
					};

        // 다음 달 버튼 클릭 시 호출되는 함수
        function next() {
            var nextMonth;
            var year = <?php echo $year; ?>;
            if (<?php echo $month; ?> === 12) {
                nextMonth = 1;
                year = <?php echo $year; ?> + 1;
            } else {
                nextMonth = <?php echo $month; ?> + 1;
            }
            updateCalendar(year, nextMonth);
          }
    </script>
    <table>
        <tr>
            <th>일</th>
            <th>월</th>
            <th>화</th>
            <th>수</th>
            <th>목</th>
            <th>금</th>
            <th>토</th>
        </tr>
        <tr>
            <?php
            //시작요일 나올떄 까지 공백출력
            for ($i = 0; $i < $firstDayWeekdayIndex; $i++) {
                echo "<td></td>";
            }

            //마지막날 전까지 반복문 돌림 1씩 늘리면서
            $Day = 1;
            while ($Day <= $daysInMonth) {
                echo "<td>$Day</td>";

                // firstDayWeekdayIndex는 최대 6, 여섯번째 왔을때 + day1 해서 7이면
                // 줄바꿈 진행
                // == 토요일일때 다음 줄로 이동
                if (($firstDayWeekdayIndex + $Day) % 7 == 0) {
                    echo "</tr><tr>";
                }

                // 다음 날로 이동
                $Day++;
            }

            ?>
        </tr>
    </table>
</div>
