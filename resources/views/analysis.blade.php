<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Analyse</title>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script src ="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src ="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    </head>
    <style type="text/css">
        .header {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 50px;
            background: #a965cb;
        }
        .container-fluid {
            margin-top: 10px;
        }
        .btn-submit {
            color: #fff;
            background-color: #a965cb;
            border-color: #a965cb;
        }
    </style>
    <script type="text/javascript">
        $.fn.DataTable.ext.pager.numbers_length = 3;

        $(document).ready(function(){
            let changeAPI = function() {
                let tempCondition = {};
                if( $("#sorting").val() != 0 ) {
                    tempCondition['sortby'] = $("#sorting").val();
                }
                if( $("#fromYear").val() != 0 ) {
                    tempCondition['fromYear'] = $("#fromYear").val();
                }
                if( $("#toYear").val() != 0 ) {
                    tempCondition['toYear'] = $("#toYear").val();
                }
                initTable(tempCondition);
            }

            let sortingOptions = ['Country','Sales','Year'];
            for( const sort of sortingOptions ) {
                $("#sorting").append(new Option(sort, sort.toLowerCase()));
            }
            for(let yearBegin = 2010; yearBegin <= 2020; yearBegin++) {
                $("#fromYear").append(new Option(yearBegin, yearBegin));
                $("#toYear").append(new Option(yearBegin, yearBegin));
            }

            $("#submit").on('click', changeAPI);

            let initTable = function(tempCondition={}){
                $("#salesPerYear").DataTable().destroy();
                $("#salesPerYear").DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    bPaginate: true,
                    ajax: function(data, callback, settings) {
                        let condition = {
                            limit: data.length,
                            offset: data.start,
                            search: data.search.value
                        };
                        if(tempCondition['sortby'] != null) condition['sortby'] = tempCondition['sortby'];
                        if(tempCondition['fromYear'] != null) condition['fromYear'] = tempCondition['fromYear'];
                        if(tempCondition['toYear'] != null) condition['toYear'] = tempCondition['toYear'];
                        $.get('/api/tempnames/show', condition, function(res) {
                            callback({
                                recordsTotal: res.totalRows,
                                recordsFiltered: res.numberOfPages,
                                data: res.data
                            });
                        });
                    },
                    aoColumns: [
                        { mData: 'country' },
                        { mData: 'sales' },
                        { mData: 'year' }
                    ],
                });
            };

            let initChart = function(){
                let ctx = document.getElementById('pieChart');
                ctx.height = 200;
                $.get('/api/tempnames/getyearwisepercentage/', function(res) {
                    if( res.error == 0 ) {
                        let dataSet = [['Year', 'Sales Percentage'], ];
                        for( const elem of res.data ){
                            dataSet.push([ 'Year-' + elem.year.toString(), parseFloat(elem.salespercentage)] );
                        }
                        google.load("visualization", "1", {packages:["corechart"]});
                        google.setOnLoadCallback(drawChart);
                        function drawChart() {
                            var data = google.visualization.arrayToDataTable(dataSet);

                            var options = {
                                title: 'Yearly Sales Percentage',
                                width: '100%',
                                height: '100%',
                                pieSliceText: 'percentage',
                                is3D: true,
                                chartArea: {
                                    left: "3%",
                                    top: "3%",
                                    height: "94%",
                                    width: "94%"
                                }
                            };
                            var chart = new google.visualization.PieChart(document.getElementById('pieChart'));
                            chart.draw(data, options);
                        }
                    }
                });
            };

            initTable();
            initChart();
        });
    </script>
    <body class="antialiased">
        <div class="header"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3">
                    <div id="filterOptions" class="card container-fluid">
                        <div class="card-body"> 
                            <h5 class="card-title">Filter Options</h5>
                            <div>
                                Sort By
                                <select id="sorting" class="custom-select">
                                    <option value="0">Please select</option>
                                </select>
                            </div>
                            <div>
                                From Year
                                <select id="fromYear" class="custom-select">
                                    <option value="0">Please select</option>
                                </select>
                            </div>
                            <div>
                                To Year
                                <select id="toYear" class="custom-select">
                                    <option value="0">Please select</option>
                                </select>
                            </div>
                            <div style="width: 100%;margin-top:10px;">
                                <button id="submit" type="button" class="btn btn-submit btn-block">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div id="tableDiv" class="card container-fluid">
                        <div class="card-body"> 
                            <div class="table-responsive">
                                <table id="salesPerYear" class="table table-striped table-hover dt-responsive display nowrap">
                                    <thead>
                                        <tr>
                                            <th>Country</th>
                                            <th>Sale</th>
                                            <th>Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid card" style="padding-left: 0px; padding-right: 0px;">
                        <div class="card-img-top header"></div>
                        <div class="card-body">
                            <div id="pieChartDiv">
                                <div id="pieChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
