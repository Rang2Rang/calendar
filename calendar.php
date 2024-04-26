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
//---------------------API적용------------------------//
	//서비스키 변수에저장
	$serviceKey = 'e59VqduelONmTZyJnlkEB97hFyqUWBaOULbvbsP03b74mKYUgA5EYuV6FDb96+KAA2ZZI3ltMN7ymNAkujjujA==';	
	//API요청 URL생성
	$url =  'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo';
	$url .= '?solYear=' . $year . '&solMonth=' . sprintf('%02d', $month) . '&ServiceKey=' . urlencode($serviceKey);
	//응답받기
	$response = file_get_contents($url);
	//simpleXML을 이용하여 XML 파싱
	$xml = new SimpleXMLElement($response);

	//for문을 돌리기위한 배열의 길이 
	$itemCount = count($xml->body->items->item);

	$result = array(); // 결과를 저장할 배열 초기화

	for ($i = 0; $i < $itemCount; $i++) {
	    $item = $xml->body->items->item[$i];
	    
	    // 특정 키를 기준으로 데이터 매핑
	    $key = (string)$item->locdate; // 예를 들어 locdate를 키로 사용
	    $value = (string)$item->dateName; // dateName을 값으로 사용
	    
	    // key(locdate)의 끝 2자리만 가져오기 (일)
	    $key = substr($key, -2);
	    // int형으로 형변환
	    $key = (int)$key;

	    // 매핑된 데이터를 결과 배열에 추가
	    $result[$key] = $value;
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
<script>
	
</script>
<div id="calendar">
    <div><?php echo $year ?>년<?php echo $month ?>월</div>
    <button onclick="prev();">이전달</button>
    <button onclick="next();">다음달</button>
<script>

	let Year = <?php echo $year; ?>;
	let Month = <?php echo $month; ?> - 1; 

	function prev() {
        if (Month === 0) {
            Year--;
            Month = 11;
        } else {
            Month--;
        }
        updateCalendar(Year, Month + 1);
        fetchHolidayInfo(Year, Month +1);
    }

function next() {
        if (Month === 11) {
            Year++;
            Month = 0;
        } else {
            Month++;
        }
        updateCalendar(Year, Month + 1);
        fetchHolidayInfo(Year, Month +1);
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
					

					// 마지막날 전까지 반복문 돌림 1씩 늘리면서
						$Day = 1;
						while ($Day <= $daysInMonth) {
				    // $result 배열에서 $Day에 해당하는 값 가져오기
				    if (isset($result[$Day])) {
				        $value = $result[$Day];
				    } else {
				        $value = '';
				    }

				    // 가져온 값이 있으면 그 값을 출력하고, 없으면 $Day를 출력
				    if ($value !== '') {
				        echo "<td>$Day$value</td>";
				    } else {
				        echo "<td>$Day</td>";
				    }

					    // firstDayWeekdayIndex는 최대 6, 여섯 번째 왔을 때 + day1 해서 7이면
					    // 줄바꿈 진행
					    // == 토요일일 때 다음 줄로 이동
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
