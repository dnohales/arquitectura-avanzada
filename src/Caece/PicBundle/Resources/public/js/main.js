Globals = {
    websocketHost: null,
    websocketPort: null
};

Lib = {
    relativeTime: function(current, previous) {
        var msPerMinute = 60 * 1000;
        var msPerHour = msPerMinute * 60;
        var msPerDay = msPerHour * 24;
        var msPerMonth = msPerDay * 30;
        var msPerYear = msPerDay * 365;

        var elapsed = current.getTime() - previous.getTime();

        if (elapsed < msPerMinute) {
             return Math.round(elapsed/1000) + ' segundos';   
        } else if (elapsed < msPerHour) {
             return Math.round(elapsed/msPerMinute) + ' minutos';   
        } else if (elapsed < msPerDay ) {
             return Math.round(elapsed/msPerHour ) + ' horas';   
        } else if (elapsed < msPerMonth) {
            return Math.round(elapsed/msPerDay) + ' días';   
        } else if (elapsed < msPerYear) {
            return Math.round(elapsed/msPerMonth) + ' meses';   
        } else {
            return Math.round(elapsed/msPerYear ) + ' años';   
        }
    }
};

Admin = {
    lastReadingByChannel: {},
    
    init: function () {
        setInterval(Admin.updateChannelValueTimes, 900);
        $(document).on("caecepic.websocket.message", function(event, data) {
            Admin.updateChannelValues(data);
        });
        
        $(document).on("caecepic.websocket.open", function(event) {
            App.websocket.send(JSON.stringify({
                command: "getLatestReadings"
            }));
        });
    },
    
    updateChannelValues: function(data) {
        for (var i in data.readings) {
            var reading = data.readings[i];
            Admin.lastReadingByChannel[reading.channel] = reading;
        }
        
        for (var i in Admin.lastReadingByChannel) {
            var reading = Admin.lastReadingByChannel[i];
            var unitName = $("#admin_channel_unit_" + reading.channel).html();
            $("#admin_channel_value_" + reading.channel).html(reading.convertedData + " " + unitName + " (raw: " + reading.rawData + ")");
        }
        
        Admin.updateChannelValueTimes();
    },
    
    updateChannelValueTimes: function() {
        for (var i in Admin.lastReadingByChannel) {
            var reading = Admin.lastReadingByChannel[i];
            $("#admin_channel_value_time_" + reading.channel).html('leído hace ' + Lib.relativeTime(new Date(), new Date(reading.readedAt)));
        }
    }
};

Home = {
    init: function() {
        $(document).on("caecepic.websocket.message", function(event, data) {
            Home.updateChannelValues(data);
        });
        
        Home.initCharts();
    },
    
    initCharts: function() {
        $('.highchart-container').each(function() {
            var channel = JSON.parse($(this).attr('data-channel-info'));
            $(this).highcharts('StockChart', {
                chart: {
                    marginRight: 10,
                },
                title: {
                    text: channel.sensor.name
                },
                subtitle: {
                    text: "Canal Nº " + channel.number
                },
                xAxis: {
                    type: 'datetime',
                    title: {
                        text: 'Tiempo'
                    }
                },
                yAxis: {
                    title: {
                        text: channel.sensor.readedDataDescription + " (" + channel.sensor.unitName + ")"
                    },
                    min: channel.beginThreshold - 100,
                    max: channel.endThreshold + 100,
                    plotLines : [{
                        value : channel.beginThreshold,
                        color : 'green',
                        dashStyle : 'shortdash',
                        width : 2,
                        label : {
                            text : 'Umbral de alarma inferior',
                            align: 'right'
                        }
                    }, {
                        value : channel.endThreshold,
                        color : 'red',
                        dashStyle : 'shortdash',
                        width : 2,
                        label : {
                            text : 'Umbral de alarma superior',
                            align: 'right'
                        }
                    }]
                },
                rangeSelector : {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                series: [{
                    name: channel.sensor.readedDataDescription,
                    data: []
                }]
            });
        });
    },
    
    updateChannelValues: function(data) {
        if (data.settingsWereChanged) {
            $("#reload_settings_dialog").modal("show");
            return;
        }

        for (var i in data.readings) {
            var reading = data.readings[i];
            
            var container = $("#chart_channel_" + reading.channel);
            var chart = Highcharts.charts[parseInt(container.attr('data-highcharts-chart'))];
            
            if (chart) {
                var series = chart.series[0];

                series.addPoint([
                    (new Date(reading.readedAt)).getTime(),
                    reading.convertedData
                ], true, false);
                
                console.log({
                    x: (new Date(reading.readedAt)).getTime(),
                    y: reading.convertedData
                });
            }
        }
    }
};

App = {
    websocket: null,
    
    init: function() {
        App.initWebsocket();
        
        $(".modal .ignore").click(function() {
            $(this).parents(".modal").modal("hide");
            return false;
        });
        
        $(".modal .reload-page").click(function() {
            window.location.reload();
            return false;
        });
    },
    
    initWebsocket: function() {
        App.websocket = new WebSocket("ws://" + Globals.websocketHost + ":" + Globals.websocketPort);
        
        App.websocket.onopen = function(event) {
            console.info("Se abrió la conexión WebSocket");
            $(document).trigger("caecepic.websocket.open");
        };
        
        App.websocket.onclose = function(event) {
            console.info("Se cerró la conexión WebSocket");
        };
        
        App.websocket.onmessage = function(event) {
            console.log(event.data);
            var data = JSON.parse(event.data);
            $(document).trigger("caecepic.websocket.message", data);
        };
        
        App.websocket.onerror = function(event) {
            console.error("Se cerró la conexión WebSocket por un error");
            $("#websocket_error_dialog").modal("show");
        };
    }
};
