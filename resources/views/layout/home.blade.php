@extends('adminlte::page')

@section('title', 'Dashboard')



@section('content')
    <div class="box box-success">
        {{--{!! Form::open(['method' => 'POST', 'url'=>'/getData' ]) !!}--}}
        <div class="box-header with-border">
            <h3 class="box-title">Select a Year</h3>

        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <select class="js-example-basic-single js-states form-control"
                                data-placeholder="Select a Organism" style="width: 100%;" tabindex="-1"
                                aria-hidden="true" id="year">
                            <option selected disabled="disabled">---Select an Option---</option>
							<option value="2011-2016">2011-2016</option>
                            <option value="2011">2011</option>
                            <option value="2012">2012</option>
                            <option value="2013">2013</option>
                            <option value="2014">2014</option>
                            <option value="2015">2015</option>
                            <option value="2016">2016</option>
                        </select></div>
            </div>
            </div>

        </div>
    </div>
<div id="graph" style="display: none;">
    <div class="box box-success">

        <div class="box-header with-border">
            <h3 class="box-title">Year <span class="year_name"></span> : Top 15 Job</h3>

        </div><!-- /.box-header -->
        <div class="box-body">
            <div id="top_job">

            </div>

        </div><!-- /.box-body -->

    </div><!-- /.box -->

    <div class="box box-primary">

        <div class="box-header with-border">
            <h3 class="box-title">Year <span class="year_name"></span> : Top 15 City</h3>

        </div><!-- /.box-header -->
        <div class="box-body">
            <div id="top_city">

            </div>

        </div><!-- /.box-body -->

    </div><!-- /.box -->

    <div class="box box-danger">

        <div class="box-header with-border">
            <h3 class="box-title">Year <span class="year_name"></span> : Top 15 States</h3>

        </div><!-- /.box-header -->
        <div class="box-body">
            <div id="top_state">

            </div>

        </div><!-- /.box-body -->

    </div><!-- /.box -->
</div>
    <input type="hidden" id="all_data" value="{{ $data }}">
@stop

@section('css')
    <link rel="stylesheet"
          href="{{ asset('vendor/lib/c3.min.css')}} ">
@stop

@section('js')
    <script src="https://d3js.org/d3.v3.js"></script>
    <script src="{{ asset('vendor/lib/c3.js') }}"></script>
    <script>
        $( document ).ready(function() {
            
            var data =  $('#all_data').val();
            data = JSON.parse(data);

            $('#year').on('change',function () {
                $('#graph').show();
                var year = $('#year').val();
                $('.year_name').text(year);
                var curData = data[year];
                var job_data = curData['top_job']['count'];
                var job_name = curData['top_job']['name'];
                var total = curData['top_job']['total'];
                if(job_data.length < 16){
                    job_data.unshift("Top 15 Jobs");
                }

                var job_chart = drawGraph("#top_job",job_data,job_name,"#66ff99",total);

                var city_data = curData['top_city']['count'];
                var city_name = curData['top_city']['name'];
                if(city_data.length < 16) {
                    city_data.unshift("Top 15 Cities");
                }
                total = curData['top_city']['total'];
                var city_chart = drawGraph("#top_city",city_data,city_name,"#3333ff",total);

                var state_data = curData['top_state']['count'];
                var state_name = curData['top_state']['name'];
                if(state_data.length < 16) {
                    state_data.unshift("Top 15 States");
                }
                total = curData['top_state']['total'];
                var city_chart = drawGraph("#top_state",state_data,state_name,"#ff3300",total);
            });

            function drawGraph(idName,count,name,barColor,total){
                var chart = c3.generate({
                    bindto : idName,
                    size: {
                        height: 480,
                    },
                    data:{
                        columns:[count] ,
                        type: 'bar'
                    },
                    bar:{
                        width: {
                            ratio: 0.5 // this makes bar width 50% of length between ticks
                        }
                    },
                    color: {
                        pattern: [barColor]
                    },
                    axis: {
                        x: {
                            type: 'category',
                            categories: name,
                            height:90
                        }
                    },
                    tooltip: {
                        format: {
                            title: function (i) {
                                return name[i] ;
                            },
                            value: function  (value, ratio, id) {
                               var percentage = (value / total) * 100;
                               percentage = percentage.toFixed(2).toString();
                                return value+"  ----  "+percentage+" % ";
                            },
                            name: function (value, ratio, id) {
                                return "Number of Job";
                            },
                        }
                    }
                });
                return chart;
            }

        });
    </script>
@stop