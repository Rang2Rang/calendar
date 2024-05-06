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
	//API요청 지난달 URL 생성
	$url_prev = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo';
	$url_prev .= '?solYear=' . $year . '&solMonth=' . sprintf('%02d', $month - 1) . '&ServiceKey=' . urlencode($serviceKey);
	$url_next = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo';
	$url_next .= '?solYear=' . $year . '&solMonth=' . sprintf('%02d', $month + 1) . '&ServiceKey=' . urlencode($serviceKey);

	//응답받기
	$response = file_get_contents($url);
	$response_prev = file_get_contents($url_prev);
	$response_next = file_get_contents($url_next);


	//simpleXML을 이용하여 XML 파싱
	$xml = new SimpleXMLElement($response);
	$xml_prev = new SimpleXMLElement($response_prev);
	$xml_next = new SimpleXMLElement($response_next);


	//for문을 돌리기위한 배열의 길이 
	if ($xml->body->items && $xml->body->items->item) {
	    $itemCount = count($xml->body->items->item);
	} else {
	    $itemCount = 0; // 아이템이 없으면 0으로 설정
	}

	if ($xml_prev->body->items && $xml_prev->body->items->item) {
	    $itemCount_prev = count($xml_prev->body->items->item);
	} else {
	    $itemCount_prev = 0; // 이전 월의 아이템이 없으면 0으로 설정
	}

	if ($xml_next->body->items && $xml_next->body->items->item) {
	    $itemCount_next = count($xml_next->body->items->item);
	} else {
	    $itemCount_next = 0; // 이전 월의 아이템이 없으면 0으로 설정
	}

	$result = array(); // 결과를 저장할 배열 

	//이번달 api 정보

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

	//지난달 api 정보

	$result_prev = array();

	for($i = 0; $i < $itemCount_prev; $i++ ){
		$item = $xml_prev->body->items->item[$i];

	    // 특정 키를 기준으로 데이터 매핑
	    $key_prev = (string)$item->locdate; // 예를 들어 locdate를 키로 사용
	    $value_prev = (string)$item->dateName; // dateName을 값으로 사용
	    
	    // key_prev(locdate)의 끝 2자리만 가져오기 (일)
	    $key_prev = substr($key_prev, -2);
	    // int형으로 형변환
	    $key_prev = (int)$key_prev;

	    // 매핑된 데이터를 결과 배열에 추가
	    $result_prev[$key_prev] = $value_prev;
	}

	//다음달 api 정보

	$result_next = array();

	for($i = 0; $i < $itemCount_next; $i++ ){
		$item = $xml_next->body->items->item[$i];

	    // 특정 키를 기준으로 데이터 매핑
	    $key_next = (string)$item->locdate; // 예를 들어 locdate를 키로 사용
	    $value_next = (string)$item->dateName; // dateName을 값으로 사용
	    
	    // key_next(locdate)의 끝 2자리만 가져오기 (일)
	    $key_next = substr($key_next, -2);
	    // int형으로 형변환
	    $key_next = (int)$key_next;

	    // 매핑된 데이터를 결과 배열에 추가
	    $result_next[$key_next] = $value_next;
	}


	//현재 달의 총 날짜
	$total_day = date('t', strtotime("$year-$month-01"));


	//요일 배열
	$weekString = array("일", "월", "화", "수", "목", "금", "토");


	//전달의 마지막 주
	$lastMonthLastDay = date('Y-m-d', strtotime("$year-$month-01 -1 day"));
	$lastMonthLastDayWeekdayIndex = date('w', strtotime($lastMonthLastDay));
	$lastMonthLastDayWeekday = $weekString[$lastMonthLastDayWeekdayIndex]; 



	//그달의 첫 째 요일(시작요일)
	$firstDayofMonth = date('Y-m-d', strtotime("$year-$month-01"));
	$firstDayWeekdayIndex = date('w', strtotime($firstDayofMonth));
	$firstDayWeekday = $weekString[$firstDayWeekdayIndex];

	//이번 달 첫째 주 시작 일
	$firstWeekStartDate = date('Y-m-d', strtotime("$year-$month-01 -$firstDayWeekdayIndex day"));

	//그달의 마지막 날짜
	$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$lastDayOfMonth = date('Y-m-d', strtotime("$year-$month-$daysInMonth"));

	//그달의 마지막 요일
	$lastDayWeekdayIndex = date('w', strtotime($lastDayOfMonth));
	$lastDayWeekday = $weekString[$lastDayWeekdayIndex];

	//지난 달의 마지막 주 정보 (날짜,요일) 배열 선언

	$prevMonthDays = array();
	for ($i = $lastMonthLastDayWeekdayIndex; $i >= 0; $i--) {
	    $prevMonthDays[] = date('d', strtotime("$lastMonthLastDay -$i day"));
	}

	// 다음 달 첫째 주 정보 (날짜, 요일) 배열 선언
	$nextMonthDays = array();
	$nextMonthStartDate = date('Y-m-d', strtotime("$year-$month-$daysInMonth +1 day"));
	$nextMonthStartDayWeekdayIndex = date('w', strtotime($nextMonthStartDate));

	for ($i = 0; $i <= 6 - $nextMonthStartDayWeekdayIndex; $i++) {
	    $date = date('Y-m-d', strtotime("$nextMonthStartDate +$i day"));
	    $day = date('j', strtotime($date));
	    $dayOfWeekIndex = date('w', strtotime($date));
	    $dayOfWeek = $weekString[$dayOfWeekIndex];
	    $nextMonthDays[] = array('date' => $day, 'day_of_week' => $dayOfWeek);
	}

	$holidays = array();

	//기본값을 false로 일단 지정
	for ($i = 1; $i <= $daysInMonth; $i++) {
		$holidays[$i] = false;
	}

	//api에서 가져온 특일을 휴일배열에 넣음
	for ($i = 0; $i < $itemCount; $i++) {
    $item = $xml->body->items->item[$i];
    $date = (int) substr($item->locdate, -2); 
    $holidays[$date] = true;
	}

	// 주말을 휴일로 처리
	for ($i = 1; $i <= $daysInMonth; $i++) {
		$dateStr = sprintf("%04d-%02d-%02d", $year, $month, $i); //확인필요
		$weekday = date('w', strtotime($dateStr)); // 0은 일요일 6은 토요일
		if ($weekday == 0 || $weekday == 6) { // 0이거나 6이면 휴일에 넣겠다.
			$holidays[$i] = true;
		}
	}

	// 3일 연속 휴일 배열
	$threeday = array();
	$threeday_surrounding = array();

	// 연속된 3일 동안 모두 휴일인지 확인
	for ($i = 1; $i <= $daysInMonth - 2; $i++) {
	    if ($holidays[$i] && $holidays[$i + 1] && $holidays[$i + 2]) {
	        $threeday[$i] = '3일연속휴일';
	        $threeday[$i+1] = '3일연속휴일';
	        $threeday[$i+2] = '3일연속휴일';
	        
	        // 3일 연속 휴일의 앞과 뒤의 날짜를 배열에 저장
	        if ($i > 1) {
	            $threeday_surrounding[] = $i - 1; // 앞 날짜 추가
	        }
	        if ($i + 3 <= $daysInMonth) {
	            $threeday_surrounding[] = $i + 3; // 뒷 날짜 추가
	        }
	    }
	}

	//연차사용시 4일 이상 휴무 배열

	$four = array();
	// 한 번에 4개를 조회했을 때 3개 이상이 휴일이면 4개 모두 배열에 담기
	for ($i = 1; $i <= $daysInMonth - 3; $i++) {
	    $count = 0;  // 휴일을 세기 위한 카운터
	    // 연속된 4일 동안 휴일인지 확인
	    for ($j = 0; $j < 4; $j++) {
	        if (!empty($holidays[$i + $j])) {
	            $count++;
	        }
	    }
	    // 3개 이상의 휴일이 있는 경우, 4일 모두 배열에 추가
	    if ($count >= 3) {
	        for ($j = 0; $j < 4; $j++) {
	            $four[] = $i + $j;  // 날짜를 배열에 추가
	        }
	    }
	}


	//순서재정렬
	$filteredFour = array_values(array_filter($four, function($day) use ($holidays) {
    return isset($holidays[$day]) && !$holidays[$day];
	}));


?>
<script>
	
</script>
<div id="calendar">
    <div id="day"><?php echo $year ?>년 <?php echo $month ?>월</div>
    <div id="btn">
	    <button id="prevbtn" onclick="prev();">이전달</button>
	    <button id="nextbtn" onclick="next();">다음달</button>
	</div>
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
	}

	function next() {
	    if (Month === 11) {
	        Year++;
	        Month = 0;
	    } else {
	        Month++;
	    }
	    updateCalendar(Year, Month + 1);
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
        <?php
			// 이전 달의 마지막 주와 이번 달의 첫째 주 출력
			echo "<tr>";
			$prev_arr = array_values($prevMonthDays);
			$result_key_arr = array_keys($result_prev);



			// prevMonthDays의 count가 7개 미만일 때만 코드 실행
			if (count($prevMonthDays) < 7) {
			    for ($i = 0; $i < count($prevMonthDays); $i++) {
			        if (isset($result_prev[$prev_arr[$i]])) {
			            // API에서 가져온 정보를 표시
			            $value = $result_prev[$prev_arr[$i]];
			            echo "<td id='prev' style='color: gray;'>{$prevMonthDays[$i]}.{$value}</td>";
			        } else{
			            // $result_key_arr에 값이 없는 경우 이전 달의 날짜를 표시
			            echo "<td id='prev' style='color: gray;'>{$prevMonthDays[$i]}</td>";
			        }
			    }
			}
			


			$remainingDays = 7 - count($prevMonthDays);

			for ($i = 1; $i <= $remainingDays; $i++) {
			    $val = '';
			    $value = '';
			    $value2 = '';

			    if (isset($threeday[$i])) {
			        $val = $threeday[$i];
			    }
			    if (isset($result[$i])) {
			        $value = $result[$i];
			    }

			    $default = $i; 

			    if ($val !== '') {
			        $default .= ",$val";
			    }
			    if ($value !== '') {
			        $default .= ",$value";
			    }
			    

			    echo "<td>$default</td>"; 
			}
			echo "</tr>";


			// 나머지 이번 달의 날짜 출력
			$Day = $remainingDays + 1; // 두 번째 주의 첫 번째 날부터 시작
			while ($Day <= $daysInMonth) {
			    // 현재 날짜에 해당하는 값 확인
			    $val = '';
			    $value = '';
			    $value_2 = '';

			    // 연차사용시4일연속휴무 표기
			    $four_text = '';
				if (in_array($Day, $filteredFour)) {
				    $four_text = '연차사용시4일연속휴무';
				}

			    if (isset($threeday[$Day])) {
			        $val = $threeday[$Day];
			    }
			    if (isset($result[$Day])) {
			        $value = $result[$Day];
			    }

			    // 날짜와 값을 결합하여 출력
			    $default = $Day;
			    if ($val !== '') {
			        $default .= ", $val";
			    }
			    if ($value !== '') {
			        $default .= ", $value";
			    }
			    if ($four_text !== '') {
			        $default .= ", $four_text";
			    }

			    echo "<td>$default</td>";
			    // 주의 마지막 요일이거나 마지막 날일 때는 줄을 바꿈
			    if ((($firstDayWeekdayIndex + $Day - 7) % 7 == 0)) {
			        echo "</tr><tr>";
			    }
			    $Day++;  // 다음 날짜로 이동
			}


			$nextMonthCount = 6 - $lastDayWeekdayIndex;
			if ($nextMonthCount > 0) {
				for ($i = 1; $i <= $nextMonthCount; $i++) {
					$nextDay = date('j', strtotime("$year-$month-$daysInMonth +$i day"));
					if (isset($result_next[$nextDay])) {
					    $nextDayContent = ". " . $result_next[$nextDay];
					} else {
					    $nextDayContent = "";
					}
					echo "<td id='next' style='color: gray;'>$nextDay{$nextDayContent}</td>";
				}
			}

			echo "</tr>";
	?>

    </table>
</div>