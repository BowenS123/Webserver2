<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Line Graph</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #fd00f1;
            padding: 1em;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
        nav ul li a {
            text-decoration: none;
            color: #000000;
            font-size: 1.2em;
            font-weight: bold;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }
        main {
            padding: 2em;
        }
        h1 {
            text-align: center;
            color: #000000;
        }
        #chart-container {
            width: 80%;
            margin: 0 auto;
        }
        canvas {
            width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>
    <header>
        <nav role="navigation">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="data.php">Data</a></li>
                <li><a href="graphic.php">Graph</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Messages Per Day</h1>
        <div id="chart-container">
            <canvas id="messageChart"></canvas>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Wachten op pagina
        document.addEventListener('DOMContentLoaded', function () {
            function refreshPage() {
                setTimeout(function() {
                    location.reload();  // Herlaad de pagina
                }, 5000);  //5000 milliseconden (5 seconden)
            }
            refreshPage();
            fetch('https://server-of-bowen.pxl.bjth.xyz/api/v1/massages.php')
                .then(response => response.json())
                .then(data => {

                    const labels = data.map(entry => new Date(entry.date).toLocaleDateString());
                    const messageCounts = data.map(entry => entry.message_count);

                    const ctx = document.getElementById('messageChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Number of Messages',
                                data: messageCounts,
                                borderColor: 'rgb(75, 192, 192)', 
                                backgroundColor: 'rgba(75, 192, 192, 0.2)', 
                                fill: false, 
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    },
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: 10
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Number of Messages'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching message chart data:', error);
                });
        });
    </script>
</body>
</html>
