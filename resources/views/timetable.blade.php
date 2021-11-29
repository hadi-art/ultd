@extends('layouts.ultd1')

@section('content')
    @php
    #dd($this_week,$slot_info,$class_info);
    $slot_number = 10;
    $day_css = "background-color:green;font-size:15px;font-weight:bold;color:#FFFFFF;";
    @endphp

    <style>
        table.blueTable {
            border: 1px solid #1C6EA4;
            background-color: #EEEEEE;
            width: 100%;
            text-align: left;
            border-collapse: collapse;
        }
        table.blueTable td, table.blueTable th {
            border: 1px solid #AAAAAA;
            padding: 3px 2px;
        }
        table.blueTable tbody td {
            font-size: 13px;
            background-color: #00ccff;
        }
        table.blueTable thead {
            background: #1508A4;
            background: -moz-linear-gradient(top, #4f46bb 0%, #2c20ad 66%, #1508A4 100%);
            background: -webkit-linear-gradient(top, #4f46bb 0%, #2c20ad 66%, #1508A4 100%);
            background: linear-gradient(to bottom, #4f46bb 0%, #2c20ad 66%, #1508A4 100%);
            border-bottom: 2px solid #444444;
        }
        table.blueTable thead th {
            font-size: 15px;
            font-weight: bold;
            color: #FFFFFF;
            border-left: 2px solid #EEF5E6;
        }
        table.blueTable thead th:first-child {
            border-left: none;
        }

        table.blueTable tfoot {
            font-size: 14px;
            font-weight: bold;
            color: #FFFFFF;
        }
        table.blueTable tfoot td {
            font-size: 14px;
        }




    </style>

    Kelas : {{$class_info->name}}
    <br>

    Minggu Semasa : {{$this_week['week_number']}}

    <table class="blueTable">
        <thead>
        <tr>
            <th>Hari/Waktu</th>
            @for($a =0;$a<$slot_number;$a++)
                <th>{{\App\Http\Controllers\HomeController::get_time_slot_info($class_info->id,$a,$year)}}</th>
            @endfor
        </thead>
{{--        <tfoot>--}}
{{--        <tr>--}}
{{--            <td colspan="11">--}}
{{--                <div class="links"><a href="#">&laquo;</a> <a class="active" href="#">1</a> <a href="#">2</a> <a href="#">3</a> <a href="#">4</a> <a href="#">&raquo;</a></div>--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        </tfoot>--}}
        <tbody>
        <tr>
            <td style={{$day_css}}>{{\App\Http\Controllers\HomeController::show_day($this_week[0]['day'],$this_week[0]['date'])}}</td>
            @for($a =0;$a<$slot_number;$a++)
                <td>{{\App\Http\Controllers\HomeController::get_subject_slot_info($class_info->id,$a,$year,$this_week[0]['day'],$week)}}</td>
            @endfor
        </tr>
        <tr>
            <td style={{$day_css}}>{{\App\Http\Controllers\HomeController::show_day($this_week[1]['day'],$this_week[1]['date'])}}</td>
            @for($a =0;$a<$slot_number;$a++)
                <td>{{\App\Http\Controllers\HomeController::get_subject_slot_info($class_info->id,$a,$year,$this_week[1]['day'],$week)}}</td>
            @endfor
        </tr>
        <tr>
            <td style={{$day_css}}>{{\App\Http\Controllers\HomeController::show_day($this_week[2]['day'],$this_week[2]['date'])}}</td>
            @for($a =0;$a<$slot_number;$a++)
                <td>{{\App\Http\Controllers\HomeController::get_subject_slot_info($class_info->id,$a,$year,$this_week[2]['day'],$week)}}</td>
            @endfor
        </tr>
        <tr>
            <td style={{$day_css}}>{{\App\Http\Controllers\HomeController::show_day($this_week[3]['day'],$this_week[3]['date'])}}</td>
            @for($a =0;$a<$slot_number;$a++)
                <td>{{\App\Http\Controllers\HomeController::get_subject_slot_info($class_info->id,$a,$year,$this_week[3]['day'],$week)}}</td>
            @endfor
        </tr>
        <tr>
            <td style={{$day_css}}>{{\App\Http\Controllers\HomeController::show_day($this_week[4]['day'],$this_week[4]['date'])}}</td>
            @for($a =0;$a<$slot_number;$a++)
                <td>{{\App\Http\Controllers\HomeController::get_subject_slot_info($class_info->id,$a,$year,$this_week[4]['day'],$week)}}</td>
            @endfor
        </tr>
        </tbody>
    </table>




    <!-- The Modal -->
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <p><table border=1>
                <tr>
                    <td>sss</td><td>xx</td>
                </tr>
                <tr>
                    <td>sss</td><td>xx</td>
                </tr>
            </table>
            </p>
        </div>

    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("myBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>



@endsection