function updateCalendar(year, month) {



   


    let html = '';


    let firstDay = new Date(year, month - 1, 1).getDay(); 

    

    html += `<div>${year}년 ${month}월</div>`;
    html += '<button onclick="prev();">이전달</button>'; // 기능구현
    html += '<button onclick="next();">다음달</button>'; // 기능구현
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


    html += '<tr>';
    // 첫날짜
    for (let i = 0; i < firstDay; i++) {
        html += '<td></td>';
    }

    let totalDays = new Date(year, month, 0).getDate();

    //day1값 잡고 1씩 계속 늘려나가며 입력
    for (let day = 1; day <= totalDays; day++) {
        html += '<td>' + day + '</td>';
        // 토요일이면 다음 행으로 넘김
        if ((firstDay + day) % 7 === 0) {
            html += '</tr><tr>';
        }
    }

        html += '</tr>';
        html += '</table>';
        document.getElementById('calendar').innerHTML = html;
    };

function fetchHolidayInfo(year, month) {
    // month를 문자열로 변환하고, 항상 두 자리 숫자로 표현
    const monthStr = String(month).padStart(2, '0');

    // 서비스 키
    const serviceKey = 'e59VqduelONmTZyJnlkEB97hFyqUWBaOULbvbsP03b74mKYUgA5EYuV6FDb96+KAA2ZZI3ltMN7ymNAkujjujA==';
    
    // URL 생성
    const url = `http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo?solYear=${year}&solMonth=${monthStr}&ServiceKey=${encodeURIComponent(serviceKey)}`;

    // fetch API 사용
    fetch(url)
        .then(response => response.text())  // 응답을 텍스트로 변환
        .then(str => { // str -> response를 text로 변환한 문자열
            // 응답 텍스트를 XML로 파싱
            const pp = new DOMParser();// 문자열을 파싱하여 DOM객체 생성
            const info = pp.parseFromString(str, "text/xml");

            // 필요한 정보 추출
            const items = info.getElementsByTagName("item");
            
            console.log(`공휴일 정보 ${year}-${monthStr}:`);
            for (let i = 0; i < items.length; i++) {
                const dateName = items[i].getElementsByTagName("dateName")[0].textContent;
                const locdate = items[i].getElementsByTagName("locdate")[0].textContent;
                console.log(`날짜: ${locdate}, 이름: ${dateName}`);
            }
        })
        .catch(error => {
            console.log("error:", error);
        });
}