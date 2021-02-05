<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do Planning</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

    <div class="container py-3">
        <div class="row">
            <div class="col">
                <table class="table table-dark table-sm">
                    <tr>
                        <th width="40%" style="vertical-align:middle">İşin Bitme Süresi (Hafta : Saat)</th>
                        <td>
                            <h4 class="m-0">{{$data['min_week']['weeks']}} Hafta : {{$data['min_week']['hours']}} Saat</h4>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="container py-3">
        <div class="row">
            @foreach($data['tasks_planning'] as $data_key => $data_val)
            <div class="col-12">
                <div class="alert alert-primary">Derece {{$data_key}}</div>
            </div>

            <div class="col-12">
                @foreach($data_val as $dev_key => $dev_val)
                <table class="table table-sm">
                    @foreach($dev_val as $week_key => $week_val)
                    <thead>
                        <tr class="alert-secondary">
                            <th colspan="2">Developer {{$dev_key}}</th>
                            <th class="text-right">({{$week_key}}. Hafta)</th>
                        </tr>
                        <tr>
                            <th scope="col">#</th>
                            <th>İş Tanımı</th>
                            <th>Süre (Saat)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($week_val as $task_key => $task_val)
                        <tr>
                            <th scope="row">{{$task_key + 1}}</th>
                            <td>{!!$task_val['name']!!}</td>
                            <td>{{$task_val['estimated_duration']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    @endforeach
                </table>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
</body>

</html>