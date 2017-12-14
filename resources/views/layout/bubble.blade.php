@extends('adminlte::page')

@section('title', 'Dashboard')

<style>
    .d3-tip {
        line-height: 1;
        font-weight: bold;
        padding: 12px;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        border-radius: 2px;
    }

    /* Creates a small triangle extender for the tooltip */
    .d3-tip:after {
        box-sizing: border-box;
        display: inline;
        font-size: 10px;
        width: 100%;
        line-height: 1;
        color: rgba(0, 0, 0, 0.8);
        content: "\25BC";
        position: absolute;
        text-align: center;
    }

    /* Style northward tooltips differently */
    .d3-tip.n:after {
        margin: -1px 0 0 0;
        top: 100%;
        left: 0;
    }
</style>


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
    <div id="graph_section" style="display: none;">
        <div id="job_graph_div">
            <div class="box box-success">

                <div class="box-header with-border">
                    <div class="col-md-4">
                        <h3 class="box-title">All Jobs <span class="year_name"></span> : </h3>

                    </div>
                    <div class="col-md-4">
                        <label>Search </label> &nbsp<input type="text" placeholder="Type Job Name" id="job_search_box"
                                                           style="width: 70%;">
                    </div>
                    <div class="col-md-4">
                        <label>Top</label> &nbsp &nbsp<input type="number" value="50" id="num_of_job"> &nbsp&nbsp
                        Jobs
                    </div>

                </div><!-- /.box-header -->
                <div class="box-body" id="job_graph">


                </div><!-- /.box-body -->

            </div><!-- /.box -->


        </div>
        <div id="city_graph_div">
            <div class="box box-success">

                <div class="box-header with-border row">
                    <div class="col-md-4">
                        <h3 class="box-title">Year <span class="year_name"></span> : </h3> &nbsp<label>For </label>&nbsp&nbsp
                        <select id="city_job_status" style="width: 70%;">
                            <option value="0">----All Jobs---</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Search </label> &nbsp<input type="text" placeholder="Type City Name" id="city_search_box"
                                                           style="width: 70%;">
                    </div>
                    <div class="col-md-4">
                        <label>Top</label> &nbsp &nbsp<input type="number" value="50" id="num_of_city"> &nbsp&nbsp
                        Cities
                    </div>

                </div><!-- /.box-header -->
                <div class="box-body" id="city_graph">


                </div><!-- /.box-body -->

            </div><!-- /.box -->


        </div>

        <div id="city_living_cost">
            <div class="box box-success">

                <div class="box-header with-border row">
                    <div class="col-md-4">
                        <h3 class="box-title"><span class="year_name"></span></h3> &nbsp<label>For </label>&nbsp&nbsp
                        <select id="city_living_cost_status" style="width: 70%;">
                            <option value="0">----All Jobs---</option>
                        </select>
                    </div>
                    {{--<div class="col-md-4">--}}
                    {{--<label>Search </label> &nbsp<input type="text" placeholder="Type City Name" id="city_search_box"--}}
                    {{--style="width: 70%;">--}}
                    {{--</div>--}}
                    {{--<div class="col-md-4">--}}
                    {{--<label>Top</label> &nbsp &nbsp<input type="number" value="50" id="num_of_city"> &nbsp&nbsp--}}
                    {{--Cities--}}
                    {{--</div>--}}

                </div><!-- /.box-header -->
                <div class="box-body" id="city_living_cost_graph">


                </div><!-- /.box-body -->

            </div><!-- /.box -->


        </div>

    </div>
    <input type="hidden" id="all_data" value="{{ $data['h1b'] }}">
    <input type="hidden" id="living_cost" value="{{ $data['living_cost'] }}">
@stop

@section('css')
    <link rel="stylesheet"
          href="{{ asset('vendor/lib/c3.min.css')}} ">
@stop

@section('js')
    <script src="https://d3js.org/d3.v3.js"></script>
    <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script src="{{ asset('vendor/lib/c3.js') }}"></script>
    <script>
        $(document).ready(function () {


            var all_data = $('#all_data').val();
            all_data = JSON.parse(all_data);
            var living_cost = $('#living_cost').val();
            living_cost = JSON.parse(living_cost);

            var livingCostGraphData = {};


            function drawLivingCostIndexBubbleChart(graphData, div, height) {
                d3.select(div).select('svg').remove();
                var data = [graphData];

                var layout = {
                    title: 'Opportunity Index vs Living Cost Affordability Index',
                    showlegend: false,
                    width: $('#' + div).width(),
                    height: height,
                    xaxis: {
                        title: 'City Opportunity Index',
                        titlefont: {
                            family: 'Courier New, monospace',
                            size: 22,
                            color: '#7f7f7f'
                        }
                    },
                    yaxis: {
                        title: 'City Affordability Index',
                        titlefont: {
                            family: 'Courier New, monospace',
                            size: 22,
                            color: '#7f7f7f'
                        }
                    }
                };


                Plotly.newPlot(div, data, layout);


            }

            function constructOpportunityVsLivingCostIndexObj(cityOpportunity, cityLivingCostIndex, radius, color) {
                var obj = {};
                // console.log( Object.keys(cityOpportunity).length);
                // console.log(Object.keys(cityLivingCostIndex).length);
                obj.x = [];
                obj.y = [];
                obj.text = [];
                obj.marker = {};
                obj.marker.color = [];
                obj.marker.size = [];
                $.each(cityLivingCostIndex, function (i, item) {
                    index = i.toUpperCase();
                    if (index in cityOpportunity) {
                        var row = {};
                        var opportunity = cityOpportunity[index];
                        var text = i + "<br>Min Salary " + opportunity.min_salary +
                            "<br>Max Salary " + opportunity.max_salary +
                            "<br>Average Salary " + opportunity.avg_salary;
                        var op_index = (opportunity.score * 100).toFixed(2);
                        obj.x.push(op_index);
                        obj.y.push(item.sorted_index);
                        obj.text.push(text);
                        var avg_index = ((parseFloat(op_index) + parseFloat(item.sorted_index)) / 2).toFixed(2);

                        obj.marker.size.push((avg_index / 100) * radius);
                        obj.marker.color.push(color)

                    }
//                    console.log(cityLivingCostIndex[index]);
//                    console.log(cityOpportunity[index]);
                });
                obj.mode = 'markers';
                return obj;


            }

            var color = ['#FF9966', '#3CE1D4'];
            var data = [];
            var jobNodeData = [];
            var cityNodeData = [];
            $('#year').on('change', function () {
                var year = $('#year').val();
                $('#graph_section').show();
                data = all_data[year];
                jobNodeData = getJobNames(data.single_job).slice(0, 50);
                cityNodeData = getJobNames(data.all_jobs.city_info);
                cityNodeData = cityNodeData.slice(0, 50);
                console.log(data.all_jobs)
                $.each(jobNodeData, function (i, item) {
                    $('#city_job_status').append($('<option>', {
                        value: item.name,
                        text: item.name
                    }));

                    $('#city_living_cost_status').append($('<option>', {
                        value: item.name,
                        text: item.name
                    }));

                    $('#num_of_city').val(cityNodeData.length);
                    $('#num_of_job').val(jobNodeData.length);
                    $('#num_of_city').on('keyup', function () {
                        var num_of_city = $('#num_of_city').val();
                        var updatedCityNode = cityNodeData.slice(0, num_of_city);
                        draw_job_bubble_chart(updatedCityNode, '#city_graph', 1);
                    });

                    $('#num_of_job').on('keyup', function () {
                        var num_of_city = $('#num_of_job').val();
                        var updatedJobNodeData = jobNodeData.slice(0, num_of_city);
                        draw_job_bubble_chart(updatedJobNodeData, '#job_graph', 0);
                    });

                    $('#city_search_box').on('keyup', function () {
                        var query = $('#city_search_box').val().toUpperCase();
                        var updatedCityNode = [];
                        $.each(cityNodeData, function (i, item) {
                            if (item.name.indexOf(query) !== -1) {
                                updatedCityNode.push(item);
                            }

                        });
                        draw_job_bubble_chart(updatedCityNode, '#city_graph', 1);

                    });

                    $('#job_search_box').on('keyup', function () {
                        var query = $('#job_search_box').val().toUpperCase();
                        var updatedJobNode = [];
                        $.each(jobNodeData, function (i, item) {
                            if (item.name.indexOf(query) !== -1) {
                                updatedJobNode.push(item);
                            }

                        });
                        draw_job_bubble_chart(updatedJobNode, '#job_graph', 0);

                    });

                    $('#city_job_status').on('change', function () {
                        var this_city = $('#city_job_status').val();

                        cityNodeData = getJobNames(data.single_job[this_city].city_info).slice(0, 50);
                        draw_job_bubble_chart(cityNodeData, '#city_graph', 1);
                    });
                    $('#city_living_cost_status').on('change', function () {

                        var this_job = $('#city_living_cost_status').val();
                        livingCostGraphData = constructOpportunityVsLivingCostIndexObj(all_data[year]['single_job'][this_job]['city_info'], living_cost, 50, 'rgb(93, 164, 214)');

                        drawLivingCostIndexBubbleChart(livingCostGraphData, 'city_living_cost_graph', 800);
                    });

                    draw_job_bubble_chart(jobNodeData, '#job_graph', 0);
                    draw_job_bubble_chart(cityNodeData, '#city_graph', 1);
                });

                livingCostGraphData = constructOpportunityVsLivingCostIndexObj(all_data[year]['all_jobs']['city_info'], living_cost, 50, 'rgb(93, 164, 214)');

                drawLivingCostIndexBubbleChart(livingCostGraphData, 'city_living_cost_graph', 800);
            });


            // $('html,body').animate({
            //         scrollTop: $("#city_graph_div").offset().top},
            //     'slow');


            function draw_job_bubble_chart(jobNodeData, divName, color_index) {

                d3.select(divName).select('svg').remove();
                var nodeSize = jobNodeData.length;
                if (nodeSize < 40) {
                    var height = 400;
                } else {
                    var height = nodeSize * 12;
                }

                var width = $(divName).width();
                var radius = 40;
                var padding = 1;

                var highRad = 70;
                var svg = d3.select(divName)
                    .append("svg")
                    .attr("height", height)
                    .attr("width", width);

                var force = d3.layout.force()
                    .gravity(.05)
                    .distance(50)
                    .charge(-40)
                    .size([width, height]);

                force.nodes(jobNodeData)
                    .start();

                var toolTips = d3.tip()
                    .attr('class', 'd3-tip')
                    .offset([-10, 0])
                    .html(function (d, i) {
                        return "<center><strong>" + d.name + "</strong><br>" +
                            "<strong>Total Job : </strong> <span style='color:green'>" + d.total_job + "</span><br>" +
                            "<strong>Job Rank : </strong> <span style='color:green'>" + (i + 1) + "</span><br>" +
                            "<strong>Job Percentage : </strong> <span style='color:green'>" + d.percentage + " % </span><br>" +
                            "<strong>Average Salary : </strong> <span style='color:green'>" + d.avg_salary + " $ </span><br></center>";
                    });

                svg.call(toolTips);


                var node = svg.selectAll(".node")
                    .data(jobNodeData)
                    .enter().append("g")
                    .attr("class", "node")
                    .call(force.drag);

                node.append("circle")
                    .attr("id", function (d) {
                        return d.name;
                    })
                    .attr("r", function (d) {
                        if (d.score < 0.05) {
                            return highRad * 0.05;
                        } else {
                            return highRad * d.score;
                        }
                    })
                    .style('fill', function (d) {
                        return color[color_index];
                    })
                    .on('dblclick', function (d) {
                        //alert('double click');
                    })
                    .on('click', function (d) {
                        //alert('single click');
                    })
                    .on('mouseover', toolTips.show)
                    .on('mouseout', toolTips.hide);

                force.on("tick", function () {

                    node.attr("transform", function (d) {
                        return "translate(" + Math.max(radius, Math.min(width - radius, d.x)) + "," + Math.max(radius, Math.min(height - radius, d.y)) + ")";
                    });
                    node.each(collide(0.3));
                });

                function getRandomInt(min, max) {
                    return Math.floor(Math.random() * (max - min + 1)) + min;
                }

                function collide(alpha) {
                    var quadtree = d3.geom.quadtree(jobNodeData);
                    return function (d) {
                        var rb = 2 * radius + padding,
                            nx1 = d.x - rb,
                            nx2 = d.x + rb,
                            ny1 = d.y - rb,
                            ny2 = d.y + rb;
                        quadtree.visit(function (quad, x1, y1, x2, y2) {
                            if (quad.point && (quad.point !== d)) {
                                var x = d.x - quad.point.x,
                                    y = d.y - quad.point.y,
                                    l = Math.sqrt(x * x + y * y);
                                if (l < rb) {
                                    l = (l - rb) / l * alpha;
                                    d.x -= x *= l;
                                    d.y -= y *= l;
                                    quad.point.x += x;
                                    quad.point.y += y;
                                }
                            }
                            return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
                        });
                    };
                }


                var randomColor = (function () {
                    var golden_ratio_conjugate = 0.618033988749895;
                    var h = Math.random();

                    var hslToRgb = function (h, s, l) {
                        var r, g, b;

                        if (s == 0) {
                            r = g = b = l; // achromatic
                        } else {
                            function hue2rgb(p, q, t) {
                                if (t < 0) t += 1;
                                if (t > 1) t -= 1;
                                if (t < 1 / 6) return p + (q - p) * 6 * t;
                                if (t < 1 / 2) return q;
                                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                                return p;
                            }

                            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                            var p = 2 * l - q;
                            r = hue2rgb(p, q, h + 1 / 3);
                            g = hue2rgb(p, q, h);
                            b = hue2rgb(p, q, h - 1 / 3);
                        }

                        return '#' + Math.round(r * 255).toString(16) + Math.round(g * 255).toString(16) + Math.round(b * 255).toString(16);
                    };
                });
            }

            function getJobNames(data) {
                var nodes = [];
                var i = 1;
                for (job in data) {
                    var node = {}
                    var obj = data[job];
                    node.name = job;
                    node.score = obj.score;
                    node.percentage = obj.percentage;
                    node.min_salary = obj.min_salary;
                    node.max_salary = obj.max_salary;
                    node.avg_salary = obj.avg_salary;
                    node.total_job = obj.total_job;
                    node.rank = i++;
                    nodes.push(node);
                }
                return nodes;
            }


        });
    </script>
@stop