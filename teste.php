<!-- <html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script>


        google.charts.load('current', {'packages':['corechart']});

        function desenharPizza (){

            var tabela = new google.visualization.DataTable();
            tabela.addColumn('string','categorias');
            tabela.addColumn('number','valores');
            tabela.addRows([

                ['Educação',2000],
                ['Transporte',500],
                ['Lazer',230],
                ['Saúde',50],
                ['Cartão de crédito',900],
                ['Alimentação',260]
            ]);

            var grafico = new google.visualization.PieChart(document.getElementById('graficoPizza'));
            grafico.draw(tabela);
    }

    google.charts.setOnLoadCallback(desenharPizza);


    </script>
</head>
<body>
    <div id="graficoPizza"></div>
</body>
</html> -->

<?php
require_once 'Classes/bancoDeDados.php';
?>