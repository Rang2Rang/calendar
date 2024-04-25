function updateCalendar(year, month) {



    let html = '';
    let btn_function = '';


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


