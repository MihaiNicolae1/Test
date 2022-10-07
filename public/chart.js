function loadIncomeChart(chartData){
    const labels = ['incomes','expenses'];
    const data = {
        labels: labels,
        datasets: [{
            label: 'Show incomes vs expenses on month',
            backgroundColor: ['rgb(2, 117, 216, 0.2)','rgb(255, 99, 132, 0.2)'],
            borderColor: ['rgb(2, 117, 216)','rgb(255, 99, 132)'],
            data: [chartData['incomes'],chartData['expenses']],
            borderWidth: 1
        }]
    };
    const config = {
        type: 'bar',
        data: data,
        options: {
            scale: {
                ticks: {
                    precision: 0
                }
            }
        }
    };
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
}
function loadPaidChart(chartData){
    const labels = ['paid','unpaid'];
    const data = {
        labels: labels,
        datasets: [{
            label: 'Show paid vs unpaid invoices on month',
            backgroundColor: ['rgb(2, 117, 216, 0.2)','rgb(255, 99, 132, 0.2)'],
            borderColor: ['rgb(2, 117, 216)','rgb(255, 99, 132)'],
            data: [chartData['paid'],chartData['unpaid']],
            borderWidth: 1
        }]
    };
    const config = {
        type: 'doughnut',
        data: data,
        options: {
            scale: {
                ticks: {
                    precision: 0
                }
            }
        }
    };
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
}
function getPaidInfo(){
    const Http = new XMLHttpRequest();
    Http.onreadystatechange = function () {
        if (Http.readyState === XMLHttpRequest.DONE) {
            let chartData = JSON.parse(Http.response);
            loadPaidChart(chartData);
        }
    }
    const url = '/invoice/paid';
    Http.open("GET", url);
    Http.send();
}

function getIncomeInfo(){
    const Http = new XMLHttpRequest();
    Http.onreadystatechange = function () {
        if (Http.readyState === XMLHttpRequest.DONE) {
            let chartData = JSON.parse(Http.response);
            loadIncomeChart(chartData);
        }
    }
    const url = '/invoice/income';
    Http.open("GET", url);
    Http.send();
}
