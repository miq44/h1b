<script>
    friendGraph: function () {
        if ($("#node-value").val() !== undefined) {

            var colors = ['red', '#3CE1D4', '#1E90FF', '#0000CC'];
            var nodes = JSON.parse($("#node-value").val());
            var links = JSON.parse($("#links-value").val());
            var strengthArray = [];
            var searchKey = '',
                percentileScore = 0,
                parameterStatus = 1;
            var linkedByIndex = {};
//  var connectionType = $('#weak').val();
            for (i = 0; i < nodes.length; i++) {
                linkedByIndex[i + "," + i] = -2;
            }
            ;

            var highestScore = 0;
            nodes.forEach(function (d) {
                highestScore = d.score > highestScore ? d.score : highestScore;
            });

            var lowestScore = highestScore;
            nodes.forEach(function (d) {
                lowestScore = d.score < lowestScore ? d.score : lowestScore;
            });
            percentileScore = lowestScore;
            var weight = highestScore / 100;
//var width = parseInt(d3.select('#friend-graph-div').style('width'), 10),
            var width = parseInt($(window).width() - 20),
                height = 1260,
                radius = 40,
                padding = 1;

            var svg = d3.select("#friend-graph-div")
                .append("svg")
                .attr("height", height)
                .attr("width", width);

            svg.append("defs").selectAll("marker")
                .data([-1, 1, 2, 3])
                .enter().append("marker")
                .attr("id", function (d) {
                    return "marker-" + d;
                })
                .attr("viewBox", "-5 -5 10 10")
                .attr("refX", 75)
                .attr("refY", 0)
                .attr("markerWidth", 4)
                .attr("markerHeight", 4)
                .attr("orient", "auto")
                .append("path")
                .attr("d", "M 0,0 m -5,-5 L 5,0 L -5,5 Z")
                .style("stroke", function (d, i) {
                    return colors[i];
                })
                .style("fill", function (d, i) {
                    return colors[i];
                });

            var force = d3.layout.force()
                .gravity(.04)
                .distance(250)
                .charge(-80)
                .size([width, height]);

            var dragStartX = 0,
                dragEndX = 0,
                dragStartY = 0,
                dragEndY = 0;


            function applyFilter() {
                var sourceIndex = [];
                var targetIndex = [];
                node.each(function (source) {
                    if (searchKey.trim().length > 0) {
                        searchKey = searchKey.trim().toUpperCase();
                        var len = searchKey.length;
                        var empId = source.employee_id.substr(0, len).toUpperCase();
                        var firstName = source.first_name.substr(0, len).toUpperCase();
                        var lastName = source.last_name.substr(0, len).toUpperCase();
                        if (source.score >= percentileScore && (searchKey == empId || searchKey == firstName || searchKey == lastName)) {
                            sourceIndex.push(source.index);
                            node.each(function (target) {
// if (linkedByIndex[source.index + "," + target.index] >= strength || linkedByIndex[ target.index + "," + source.index ] >= strength) {
                                if (isStrengthMatch(linkedByIndex[source.index + "," + target.index]) || isStrengthMatch(linkedByIndex[target.index + "," + source.index])) {
                                    targetIndex.push(target.index);
                                }
                            });
                        }
                    } else {
                        if (source.score >= percentileScore) {
                            sourceIndex.push(source.index);
                            node.each(function (target) {
//    if (linkedByIndex[source.index + "," + target.index] >= strength || linkedByIndex[ target.index + "," + source.index ] >= strength) {
                                if (isStrengthMatch(linkedByIndex[source.index + "," + target.index]) || isStrengthMatch(linkedByIndex[target.index + "," + source.index])) {
                                    targetIndex.push(target.index);
                                }
                            });
                        }
                    }
                });
                node.style("opacity", function (source) {
                    var op = 0.1;
                    targetIndex.forEach(function (value) {
                        if (value == source.index) {
                            op = 0.4;
                        }
                    });
                    sourceIndex.forEach(function (value) {
                        if (value == source.index) {
                            op = 1;
                        }
                    });

                    return op;
                });
                link.style('opacity', function (d) {
                    var op = 0.01;
                    sourceIndex.every(function (index) {
//   if (d.value >= strength && (d.source.index == index || d.target.index == index)) {
                        if (isStrengthMatch(d.value) && (d.source.index == index || d.target.index == index)) {
                            op = 1;
                            return false;
                        }
                        return true;
                    });
                    return op;
                });
            }

            function isStrengthMatch(index) {
                if (strengthArray.length === 0) {
                    return true;
                }
                var match = false;
                strengthArray.every(function (val) {
                    if (val === index) {
                        match = true;
                        return false;
                    }
                    return true;
                });
                return match;
            }


            $('.strength').on('click', function () {
                if (parameterStatus === 1) {
                    var value = $(this).val();
                    if ($(this).is(':checked')) {
                        strengthArray.push(value);
                        applyFilter();
                    } else {
                        var index = strengthArray.indexOf(value);
                        if (index >= 0) {
                            strengthArray.splice(index, 1);
                        }
                        applyFilter();
                    }
                }
            });

            $('#friend-graph-reset-button').on('click', function () {
                strengthArray.length = 0;
                searchKey = '';
                percentileScore = lowestScore;
                parameterStatus = 1;
                $('.strength').prop('checked', false);
                $('#slider-range-max-1').slider('value', 100);
                $("#slider-value-1").html(100 + '%');
                $('.strength').prop('disabled', false);
                $('#search-by-name').val('');
                $('#search-by-name').prop('disabled', false);
                $('#slider-value-1').html(100 + '%');
                $('#slider-range-max-1').slider('value', 100);
                $('#slider-range-max-1').slider({
                    disabled: false
                });
                applyFilter();
            });


            $('#search-by-name').on('keyup', function () {
                if (parameterStatus === 1) {
                    searchKey = $('#search-by-name').val();
                    applyFilter();
                }
            });
            $("#slider-range-max-1").slider({
                step: 5,
                range: "max",
                min: 5,
                max: 100,
                value: 100,
                slide: function (event, ui) {
                    if (parameterStatus === 1) {
                        var percentile = ui.value;
                        percentile = (100 - percentile) * weight;
                        percentileScore = percentile == 0 ? lowestScore : percentile;
                        applyFilter();
                    }
                    $("#slider-value-1").html(ui.value + '%');
                }
            });
            $("#slider-value-1").html($("#slider-range-max-1").slider("value") + '%');

            force.nodes(nodes)
                .links(links)
                .start();


            function resize() {
//width = parseInt(d3.select('#friend-graph-div').style('width'), 10);
                width = parseInt($(window).width()),
                    force.stop();
                svg.attr('width', width)
                    .attr('height', height);
                force.size([width, height])
                    .nodes(nodes)
                    .links(links)
                    .start();
            }

// d3.select(window).on('resize', resize);
            window.addEventListener("resize", resize)
            var link = svg.selectAll(".link")
                .data(links)
                .enter().append("line")
                .style("stroke-width", function (d) {
                    return "2px";
                })
                .style("stroke", function (d) {

                    if (d.value == -1) {
                        return colors[0];
                    } else {
                        return colors[d.value];
                    }
                })
                .style("marker-end", function (d) {
                    return "url(#marker-" + d.value + ")";
                });

            var node = svg.selectAll(".node")
                .data(nodes)
                .enter().append("g")
                .attr("class", "node")
                .call(force.drag);

            node.append("clipPath")
                .attr('id', "clipPath")
                .append('circle')
                .attr("cx", "0")
                .attr("cy", "-10")
                .attr("r", "30")
                .style("stroke", "black")
                .style("stroke-width", "1.5794");

            node.append("image")
                .attr("clip-path", "url(#clipPath)")
                .attr("xlink:href", function (d) {
                    return d.image_path;
                })
                .attr("x", -40)
                .attr("y", -40)
                .attr("width", 80)
                .attr("height", 80)
                .on('dblclick', function (d) {
                    window.location = "/officer/userdetails/view/" + d.employee_id;
                })
                .on('click', function (d) {
                    d3.event.stopPropagation();
                })
                .on('mouseover', function (d) {
                    toolTips.transition()
                        .duration(200)
                        .style("opacity", .9);
                    toolTips.html(d.last_name + " " + d.first_name + "<br/>" + d.employee_id + "<br/>" + $('#score').val() + " : " + d.score)
                        .style("left", (d.x + 8) + "px")
                        .style("top", (d.y - 90) + "px");
                })

                .on('mouseleave', function (d) {
                    toolTips.transition()
                        .duration(500)
                        .style('opacity', '0');
                });

            var drag = force.drag()
                .on("dragstart", function (d) {
                    dragStartX = d.x;
                    dragStartY = d.y;
                })
                .on("dragend", function (d) {
                    dragEndX = d.x;
                    dragEndY = d.y;

                    if (Math.abs(dragStartX - dragEndX) <= 5 && Math.abs(dragStartY - dragEndY) <= 5) {
                        $('#search-by-name').val('');
                        $('#search-by-name').prop('disabled', true);
                        $('#slider-range-max-1').slider('value', 100);
                        percentileScore = lowestScore;
                        strengthArray.length = 0;
                        $("#slider-value-1").html(100 + '%');
                        $("#slider-range-max-1").slider({
                            disabled: true
                        });
                        $('.strength').prop('checked', false);
                        $('.strength').prop('disabled', true);
                        searchKey = d.employee_id;
                        parameterStatus = 0;
                        applyFilter();
                    } else {
// console.log("drag"); // Drag event
                    }
                });

            svg.on('click', function () {
                if (parameterStatus == 0) {
                    $("#slider-range-max-1").slider({
                        disabled: false
                    });
                    $('#search-by-name').prop('disabled', false);
                    $('.strength').prop('disabled', false);
                    searchKey = $('#search-by-name').val();
                    parameterStatus = 1;
                    var percentile = $('#slider-range-max-1').slider('option', 'value');
                    $('.strength').each(function () {
                        var value = $(this).val();
                        if ($(this).is(':checked')) {
                            strengthArray.push(value);
                        }
                    });
                    percentile = (100 - percentile) * weight;
                    percentileScore = percentile == 0 ? lowestScore : percentile;
                    applyFilter();
                }
            });

            links.forEach(function (d) {
                linkedByIndex[d.source.index + "," + d.target.index] = d.value;
            });
            var toolTips = d3.select("#friend-graph-div").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0);


            force.on("tick", function () {
                link.attr("x1", function (d) {
                    return d.source.x;
                })
                    .attr("y1", function (d) {
                        return d.source.y;
                    })
                    .attr("x2", function (d) {
                        return d.target.x;
                    })
                    .attr("y2", function (d) {
                        return d.target.y;
                    });
                node.attr("transform", function (d) {
                    return "translate(" + Math.max(radius, Math.min(width - radius, d.x)) + "," + Math.max(radius, Math.min(height - radius, d.y)) + ")";
                });
                node.each(collide(0.3));
            });


            function collide(alpha) {
                var quadtree = d3.geom.quadtree(nodes);
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
        }
    }
    ,
</script>