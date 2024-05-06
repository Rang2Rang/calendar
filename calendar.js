function updateCalendar(year, month) {
    const monthStr = String(month).padStart(2, '0');
    const monthStr_prev = String(month-1).padStart(2, '0');
    const monthStr_next = String(month+1).padStart(2, '0');
    const serviceKey = 'e59VqduelONmTZyJnlkEB97hFyqUWBaOULbvbsP03b74mKYUgA5EYuV6FDb96+KAA2ZZI3ltMN7ymNAkujjujA==';
    const url = `http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo?solYear=${year}&solMonth=${monthStr}&ServiceKey=${encodeURIComponent(serviceKey)}`;
    const url_next = `http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo?solYear=${year}&solMonth=${monthStr_next}&ServiceKey=${encodeURIComponent(serviceKey)}`;
    const url_prev = `http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo?solYear=${year}&solMonth=${monthStr_prev}&ServiceKey=${encodeURIComponent(serviceKey)}`;
    const daysInMonth = new Date(year, month, 0).getDate();

    let result = {};
    let result_prev = {};
    let result_next = {};

    fetch(url)
        .then(response => response.text())
        .then(str => {
            const pp = new DOMParser();
            const info = pp.parseFromString(str, "text/xml");

            const items = info.getElementsByTagName("item");

            console.log(`공휴일  ${year}-${monthStr}:`);

            for (let i = 0; i < items.length; i++) {
                const dateName = items[i].getElementsByTagName("dateName")[0].textContent;
                const locdate = items[i].getElementsByTagName("locdate")[0].textContent;

                const key = locdate.slice(-2);

                result[key] = dateName;
            }

            fetch(url_prev)
                .then(response => response.text())
                .then(str2 => {
                    const dom = new DOMParser();
                    const info_prev = dom.parseFromString(str2, "text/xml");

                    const items_prev = info_prev.getElementsByTagName("item");

                    console.log(`지난달공휴일 ${year}-${monthStr_prev}:`);

                    for(let i = 0; i < items_prev.length; i++){
                        const dateName = items_prev[i].getElementsByTagName("dateName")[0].textContent;
                        const locdate = items_prev[i].getElementsByTagName("locdate")[0].textContent;

                        const key = locdate.slice(-2);

                        result_prev[key] = dateName;
                    }

                    fetch(url_next)
                        .then(response => response.text())
                        .then(str3 => {
                            const oo = new DOMParser();
                            const info_next = oo.parseFromString(str3, "text/xml");

                            const items_next = info_next.getElementsByTagName("item");

                            console.log(`다음달공휴일 ${year}-${monthStr_next}:`);

                            for(let i = 0; i < items_next.length; i++){
                                const dateName = items_next[i].getElementsByTagName("dateName")[0].textContent;
                                const locdate = items_next[i].getElementsByTagName("locdate")[0].textContent;

                                const key = locdate.slice(-2);

                                result_next[key] = dateName;
                            }
                            const { threeday, filteredFour } = findholiday(result, year, month, daysInMonth, items);
                            updateHTML(result, result_prev, result_next, year, month, threeday, filteredFour);

                        })
                        .catch(error => {
                            console.log("error:", error);
                        });
                });
        });
}



function findholiday(holidayData, year, month, daysInMonth, items) {
    // 주말포함 휴일배열
    // 기본값 false
    const holiArray = {};
    for (let i = 1; i <= daysInMonth; i++) {
        holiArray[i] = false;
    }

    // api로 가져온 특일을 휴일 배열에 넣음
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        const data = parseInt(item.getElementsByTagName("locdate")[0].textContent.slice(-2));
        holiArray[data] = true;
    }

    // 주말을 휴일로 처리
    for (let i = 1; i <= daysInMonth; i++) {
        const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        const dateObj = new Date(dateStr);
        const weekday = dateObj.getDay();
        if (weekday == 0 || weekday == 6) {
            holiArray[i] = true;
        }
    }

    // 3일연속 휴일배열
    const threeday = [];
    // 3일연속 휴일인지 확인
    for (let i = 1; i <= daysInMonth - 2; i++) {
        if (holiArray[i] && holiArray[i + 1] && holiArray[i + 2]) {
            threeday[i] = '3일연속휴일';
            threeday[i + 1] = '3일연속휴일';
            threeday[i + 2] = '3일연속휴일';
        }
    }
    let four = [];
    // 한 번에 4개를 조회했을 때 3개 이상이 휴일이면 4개 모두 배열에 담기
    for (let i = 1; i <= daysInMonth - 3; i++) {
        let count = 0;  // 휴일을 세기 위한 카운터
        // 연속된 4일 동안 휴일인지 확인
        for (let j = 0; j < 4; j++) {
            if (holiArray[i + j]) {
                count++;
            }
        }
        // 3개 이상의 휴일이 있는 경우, 4일 모두 배열에 추가
        if (count >= 3) {
            for (let j = 0; j < 4; j++) {
                four.push(i + j);  // 날짜를 배열에 추가
            }
        }
    }

    // 순서 재정렬
    let filteredFour = four.filter(day => !holiArray[day]);

    console.log(filteredFour);

    return { threeday: threeday, filteredFour: filteredFour };
}






function updateHTML(result, result_prev, result_next, year, month, threeday, filteredFour) {
    //next함수 기준
    //next가 할떄 month + 1 인채로 넘어오니 
    //month-1의 달 마지막 정보들을 가져와야함
    const weekString = ["일", "월", "화", "수", "목", "금", "토"];

    const lastDayOfPrevMonth = new Date(year, month - 1, 0); 

    const daysInPrevMonth = lastDayOfPrevMonth.getDate(); // 이전 달의 총 날짜 수

    // 이전 달의 마지막 주의 요일
    const lastDayOfWeekOfPrevMonth = lastDayOfPrevMonth.getDay(); // 0부터 일요일, 6은 토요일

    // 이전 달의 마지막 주의 날짜와 요일 정보를 배열에 저장
    const PrevMonthLastWeekCount = lastDayOfWeekOfPrevMonth + 1;

    //지난달 정보를 가진 배열 생성
    const prevMonthDays = [];

    for (let i = lastDayOfWeekOfPrevMonth; i >= 0; i--) {
        const date = daysInPrevMonth - lastDayOfWeekOfPrevMonth + i; // 이전 달의 마지막 주의 날짜
        const dayOfWeek = weekString[i]; // 이전 달의 마지막 주의 요일
        prevMonthDays.push({ date: date, dayOfWeek: dayOfWeek });
    }

    const firstDayOfNextMonth = new Date(year, month, 1);

    const daysInNextMonth = new Date(year, month + 1, 0).getDate(); // 다음 달의 총 날짜 수

    //다음 달의 첫 주의 요일
    const firstDayOfWeekOfNextMonth = firstDayOfNextMonth.getDay(); // 0 = 일 6 = 토

    const NextMonthFirstWeekCount = firstDayOfWeekOfNextMonth + 1;

    //다음 달의 첫 주를 출력
    const nextMonthFirstWeek = [];

    for (let i = 1; i <= 7 - firstDayOfWeekOfNextMonth; i++) {
        const date = i; // 다음 달의 첫 주의 날짜
        const dayOfWeek = weekString[(firstDayOfWeekOfNextMonth + i) % 7]; // 다음 달의 첫 주의 요일
        nextMonthFirstWeek.push({ date: date, dayOfWeek: dayOfWeek });
    }


    // 업데이트로 그려줄 HTML 생성
    let html = '';

    const firstDay = new Date(year, month - 1, 1).getDay();

    //연차사용시 4일연속쉴수 있는 구간 구하기



    html += `<div id="day">${year}년 ${month}월</div>`;
    html += '<div id="btn">';
    html += '<button id="prevbtn" onclick="prev();">이전달</button>'; // 기능구현
    html += '<button id="nextbtn" onclick="next();">다음달</button>'; // 기능구현
    html += '</div>';
    html += '<table>';
    html += '<tr>';
    html += '<th>일</th>';
    html += '<th>월</th>';
    html += '<th>화</th>';
    html += '<th>수</th>';
    html += '<th>목</th>';
    html += '<th>금</th>';
    html += '<th>토</th>';
    html += '</tr>';


    const remain = 7 - PrevMonthLastWeekCount;
    // 첫줄내용
    if (PrevMonthLastWeekCount < 7) { 
        for (let i = lastDayOfWeekOfPrevMonth; i >= 0; i--) {
            const date = prevMonthDays[i].date;
            const value = result_prev[date]; // 해당 날짜에 연관된 값이 있는지 확인

            if (value) {
                html += `<td id="prev" style='color: gray;'>${date}, ${value}</td>`; // 날짜와 값 모두 표시
            } else {
                html += `<td id="prev" style='color: gray;'>${date}</td>`; // 날짜만 표시
            }
        }
        for (let i = 1; i < remain + 1; i++) {
            const youngday = String(i).padStart(2, '0');
            const value_2 = result[youngday]; // 해당 날짜에 연관된 값이 있는지 확인

            if (value_2) {
                html += `<td>${i}, ${value_2}</td>`; // 날짜와 값 모두 표시
            } else {
                html += `<td>${i}</td>`; // 날짜만 표시
            }
        }
        html += '</tr>';
    }
    const totalDays = new Date(year, month, 0).getDate();
    // 둘째줄 부터 나머지
    for (let day = remain + 1; day <= totalDays; day++) {

        const youngday = String(day).padStart(2,'0');
        const holiday = result[youngday]; // 휴일 정보 가져오기

        let value = '';
        let holidayText = ''; 
        let fourText = '';

        // 휴일이 있는 경우
        if (holiday) {
            holidayText = holiday;
        }

        // 연속된 휴일 그룹이 있는 경우
        if (threeday[day]) {
            const holiholi = threeday[day];

            if (holiholi.length >= 3) {
                if (holidayText) {
                    holidayText += `, 3일 연속 휴무`;
                } else {
                    holidayText = `3일 연속 휴무`;
                }
            }
        }
        //filterdFour값이 있는경우 '연차 사용시 4일 연속 휴무' 텍스트 띄움
        if(filteredFour.includes(day)){
            fourText = '연차 사용시 4일 연속 휴무';
        }else{
            
        }

        // 날짜 , 휴일정보 추가
        if (holidayText !== '') {
            html += `<td>${day}.${holidayText}${fourText ? `, ${fourText}` : ''}</td>`;
        } else {
            html += `<td>${day}${fourText ? `, ${fourText}` : ''}</td>`;
        }


        // 토요일인 경우 다음 행으로 넘김
        if ((firstDay + day) % 7 === 0) {
            html += '</tr><tr>';
        }
    }
    //마지막 줄에 다음달 정보 추가
    if(nextMonthFirstWeek.length < 7){
        for (let i = 0; i < nextMonthFirstWeek.length; i++) {
            const rawDate = nextMonthFirstWeek[i].date;
            const date = rawDate.toString().padStart(2, '0');
            const value3 = result_next[date];

            if (value3){
                html += `<td id="next" style='color: gray;'>${rawDate},${value3}</td>`;
            } else {
                html += `<td id="next" style='color: gray;'>${rawDate}</td>`;
            }
        }
    html += '</tr>';
    html += '</table>';
    document.getElementById('calendar').innerHTML = html;
}
}