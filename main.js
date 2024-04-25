function search(){


    let year = $('#year').val();
    let month = $('#month').val();

    let senddata = new Object();

    senddata.year = year;
    senddata.month = month;

    render('calendar', senddata);  

   };

