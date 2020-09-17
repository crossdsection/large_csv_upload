<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Analyse</title>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src = "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
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
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#salesPerYear").DataTable({
                "processing": true,
                "serverSide": true,
                "bPaginate": true,
                "ajax": function(data, callback, settings) {
                    console.log(data);
                    $.get('/api/tempnames/show', {
                        limit: data.length,
                        offset: data.start,
                        search: data.search.value
                    }, function(res) {
                        callback({
                            recordsTotal: res.totalRows,
                            recordsFiltered: res.numberOfPages,
                            data: res.data
                        });
                    });
                },
                aoColumns: [
                    { mData: 'id' },
                    { mData: 'country' },
                    { mData: 'sales' },
                    { mData: 'year' }
                ],
            });
        });
    </script>
    <body class="antialiased">
        <div class="header"></div>
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <div id="filterOptions">
                        <h1>Filter Options</h1>
                        <div>Sort By</div>
                        <div>From Year</div>
                        <div>To Year</div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div id="tableDiv">
                        <table id="salesPerYear">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Country</th>
                                    <th>Sale</th>
                                    <th>Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                    <div id="pieChartDiv"></div>
                </div>
            </div>
        </div>
    </body>
</html>
